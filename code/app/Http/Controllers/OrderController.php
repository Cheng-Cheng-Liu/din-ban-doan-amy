<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Meal;
use App\Models\Wallet;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Contracts\RestaurantInterface;
use App\Models\WalletLog;
use App\Http\Requests\CreateOrderRequest;

class OrderController extends Controller
{
    public function create_order(RestaurantInterface $restaurant, CreateOrderRequest $request)
    {
        $userName = $request->input('user_name');
        $phone = $request->input('phone');
        $restaurantId = $request->input('restaurant_id');
        $amount = $request->input('amount');
        $status = $request->input('status');
        $remark = $request->input('remark');
        $pickUpTime = $request->input('pick_up_time');
        $createdTime = $request->input('created_time');
        $detail = $request->input('detail');

        // 再計算一次各個商品的數量*(資料庫裡的)單價最後的總額有沒有符合前端送來的amount
        (int)$count = 0;
        foreach ($detail as $oneDetail) {
            $mealPrice = Meal::where('another_id', '=', $oneDetail['another_id'])->where('restaurant_id', '=', $restaurantId)->first()->price;
            $count = $count + (int)$oneDetail['quantity'] * (int)$mealPrice;
        }
        if ($count != $amount) { {

                return ['error' => __('error.totalAmountWrong')];
            }
        }

        // cache lock start
        $lock = Cache::lock('foo' . $userName, 10);

        if ($lock->get()) {
            try {
                // wallet.balance>amount(總額)
                $walletBalance = Wallet::where('user_id', '=', Auth::user()->id)->where('status', 1)->sum('balance');
                if ($walletBalance < $amount) {
                    return ['error' => __('error.walletBalanceNotEnough')];
                }

                // 生成UUID
                $uuid = (string) Str::uuid();

                // 店家回傳成功收到訂單?
                $restaurantResponse = $restaurant->sendOrder([
                    'user_name' => $userName,
                    'phone' => $phone,
                    'restaurant_id' => $restaurantId,
                    'amount' => $amount,
                    'status' => $status,
                    'remark' => $remark,
                    'pick_up_time' => $pickUpTime,
                    'created_time' => $createdTime,
                    'detail' => $detail,
                    'uuid' => $uuid,
                ]);

                if ($restaurantResponse != 0) {
                    return ['error' => $restaurantResponse];
                }

                // 修改資料表orders、order_details、wallet_logs、wallets start
                DB::beginTransaction();
                // orders
                $order = Order::create([
                    'user_id' => Auth::user()->id,
                    'restaurant_id' => $restaurantId,
                    'name' => $userName,
                    'another_id' => $uuid,
                    'choose_payment' => 'credit',
                    'amount' => $amount,
                    'status' => 1,
                    'remark' => $remark,
                    'pick_up_time' => $pickUpTime,
                    'created_at' => $createdTime,
                ]);

                // order_details
                foreach ($detail as $onemeal) {
                    // var_dump($onemeal[ 'meal_name']);
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'meal_id' => $onemeal['meal_id'],
                        'meal_another_id' => $onemeal['another_id'],
                        'price' => $onemeal['price'],
                        'quantity' => $onemeal['quantity'],
                        'amount' => $onemeal['amount'],
                        'remark' => $onemeal['meal_remark'],
                        'created_at' => $createdTime,
                    ]);
                }

                // 先拿所有TYPE的錢包們
                $wallets = Wallet::where('user_id', '=', Auth::user()->id)->where('status', '=', 1)->orderBy('wallet_type', 'desc')->get()->toArray();

                // 先扣非主錢包，從type大的開始減
                $totalAmount = $amount;
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
                        'user_id' => Auth::user()->id,
                        'wallet_id' => $wallet['id'],
                        'order_id' => $order->id,
                        'amount' => -$walletLogAmount,
                        'balance' => Wallet::find($wallet['id'])->balance,
                        'status' => 1,
                        'remark' => '',
                        'created_at' => $createdTime,
                    ]);


                    $totalAmount = $totalAmount - $wallet['balance'];
                    if ($totalAmount <= 0) {
                        break;
                    }
                }
                // 修改資料表orders、order_details、wallet_logs、wallets end
                DB::commit();
                return ['error' => __('error.success')];
            } catch (\Exception $e) {
                // 修改資料表orders、order_details、wallet_logs、wallets 失敗回滾
                DB::rollBack();
                Log::channel('sendOrder')->info($e);
            } finally {

                $lock->release();
            }
        }
        // cache lock end


    }
}
