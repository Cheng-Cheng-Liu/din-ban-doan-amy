<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Wallet;
use App\Models\WalletLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $data = $this->data;

            // 檢查餘額
            $walletBalance = Wallet::where('user_id', '=', $data['user_id'])->where('status', 1)->sum('balance');
            if ($walletBalance < $data['amount']) {
                Log::channel('sendOrder')->error(__('error.walletBalanceNotEnough'));
                return;
            }

            // 生成 UUID
            $uuid = (string) Str::uuid();

            // 店家回傳成功收到訂單
            $restaurantResponse = $data['restaurant']->sendOrder([
                'user_name' => $data['user_name'],
                'phone' => $data['phone'],
                'restaurant_id' => $data['restaurant_id'],
                'amount' => $data['amount'],
                'status' => $data['status'],
                'remark' => $data['remark'],
                'pick_up_time' => $data['pick_up_time'],
                'created_time' => $data['created_time'],
                'detail' => $data['detail'],
                'uuid' => $uuid,
            ]);

            if ($restaurantResponse == 0) {
                Log::channel('sendOrder')->error($restaurantResponse);
                return;
            }

            DB::beginTransaction();

            // 儲存訂單
            $order = Order::create([
                'user_id' => $data['user_id'],
                'restaurant_id' => $data['restaurant_id'],
                'name' => $data['user_name'],
                'another_id' => $uuid,
                'choose_payment' => 'credit',
                'amount' => $data['amount'],
                'status' => 1,
                'remark' => $data['remark'],
                'pick_up_time' => $data['pick_up_time'],
                'created_at' => $data['created_time'],
            ]);

            // 儲存訂單詳情
            foreach ($data['detail'] as $onemeal) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'meal_id' => $onemeal['meal_id'],
                    'meal_another_id' => $onemeal['another_id'],
                    'price' => $onemeal['price'],
                    'quantity' => $onemeal['quantity'],
                    'amount' => $onemeal['amount'],
                    'remark' => $onemeal['meal_remark'],
                    'created_at' => $data['created_time'],
                ]);
            }

            // 處理錢包餘額
            $wallets = Wallet::where('user_id', '=', $data['user_id'])->where('status', '=', 1)->orderBy('wallet_type', 'desc')->get()->toArray();
            $totalAmount = $data['amount'];
            foreach ($wallets as $wallet) {
                if ($totalAmount > $wallet['balance']) {
                    Wallet::where('id', $wallet['id'])->update(['balance' => 0]);
                    $walletLogAmount = $wallet['balance'];
                } else {
                    Wallet::where('id', $wallet['id'])->update([
                        'balance' => $wallet['balance'] - $totalAmount,
                    ]);
                    $walletLogAmount = $totalAmount;
                }

                WalletLog::create([
                    'user_id' => $data['user_id'],
                    'wallet_id' => $wallet['id'],
                    'order_id' => $order->id,
                    'amount' => -$walletLogAmount,
                    'balance' => Wallet::find($wallet['id'])->balance,
                    'status' => 1,
                    'remark' => '',
                    'created_at' => $data['created_time'],
                ]);

                $totalAmount = $totalAmount - $wallet['balance'];
                if ($totalAmount <= 0) {
                    break;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('sendOrder')->error($e->getMessage());
        }
    }
}
