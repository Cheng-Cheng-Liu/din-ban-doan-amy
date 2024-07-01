<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
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
        $lock = Cache::lock('foo', 100);
        if ($lock->get()) {
            // wallet.balance>amount(總額)
            $walletBalance = Wallet::where('user_id', '=', Auth::user()->id)->where('status', 1)->sum('balance');
            if ($walletBalance < $amount) {
                $lock->release();

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
                $lock->release();

                return ['error' => $restaurantResponse];
            }

            // 成功修改資料表orders、order_details、wallet_logs、wallets
            // orders
            $order = new Order;
            $order->user_id = Auth::user()->id;
            $order->restaurant_id = $restaurantId;
            $order->name = $userName;
            $order->another_id = $uuid;
            $order->choose_payment = 'creidt';
            $order->amount = $amount;
            $order->status = 1;
            $order->remark = $remark;
            $order->pick_up_time = $pickUpTime;
            $order->created_at = $createdTime;
            $order->save();

            // order_details
            foreach ($detail as $onemeal) {
                // var_dump($onemeal[ 'meal_name']);
                $orderDetail = new OrderDetail;
                $orderDetail->order_id = $order->id;
                $mealId = Meal::where('restaurant_id', '=', $restaurantId)->where('another_id', '=', $onemeal['another_id'])->first()->id;
                $orderDetail->meal_id = $mealId;
                $orderDetail->meal_another_id = $onemeal['another_id'];
                $orderDetail->price = $onemeal['price'];
                $orderDetail->quantity = $onemeal['quantity'];
                $orderDetail->amount = $onemeal['amount'];
                $orderDetail->remark = $onemeal['meal_remark'];
                $orderDetail->created_at = $createdTime;
                $orderDetail->save();
            }
            // 先拿所有TYPE的錢包們
            $wallets = Wallet::where('user_id', '=', Auth::user()->id)->where('status', '=', 1)->orderBy('wallet_type', 'desc')->get()->toArray();

            // 先扣非主錢包，從type大的開始減
            $totalAmount = $amount;
            foreach ($wallets as $wallet) {

                $myWallet = Wallet::where('id', '=', $wallet['id'])->first();
                if ($totalAmount > $wallet['balance']) {
                    $myWallet->balance = 0;
                    $walletLogAmount = $wallet['balance'];
                } else {
                    $myWallet->balance = $wallet['balance'] - $totalAmount;
                    $walletLogAmount = $totalAmount;
                }
                $myWallet->save();

                $walletLog = new WalletLog;
                $walletLog->user_id = Auth::user()->id;
                $walletLog->wallet_id = $wallet['id'];
                $walletLog->order_id = $order->id;
                $walletLog->amount = -$walletLogAmount;
                $walletLog->balance = $myWallet->balance;
                $walletLog->status = 1;
                $walletLog->remark = '';
                $walletLog->created_at = $createdTime;
                $walletLog->save();

                $totalAmount = $totalAmount - $wallet['balance'];
                if ($totalAmount <= 0) {
                    break;
                }
            }
            $lock->release();
        }
        // cache lock end

        return ['error' => __('error.success')];
    }
}
