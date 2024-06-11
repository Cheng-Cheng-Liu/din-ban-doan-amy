<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Meal;
use App\Models\Wallet;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\Restaurants\SteakHome;
use App\Services\Restaurants\Oishii;
use App\Services\Restaurants\Tasty;
use App\Contracts\RestaurantInterface;
use App\Models\WalletLog;
use Exception;

class OrderController extends Controller
{
    public $user_name;
    public $phone;
    public $restaurant_id;
    public $amount;
    public $status;
    public $remark;
    public $pick_up_time;
    public $created_time;
    public $detail = [];


    public function __construct(Request $request)
    {
        $this->user_name = $request->input("user_name");
        $this->phone = $request->input("phone");
        $this->restaurant_id = $request->input("restaurant_id");
        $this->amount = $request->input("amount");
        $this->status = $request->input("status");
        $this->remark = $request->input("remark");
        $this->pick_up_time = $request->input("pick_up_time");
        $this->created_time = $request->input("created_time");
        $this->detail = $request->input("detail");
    }
    public function create_order(RestaurantInterface $restaurant)
    {
        // 檢查參數正確嗎?
        $checkParameter = $this->checkParameter();

        if (!$checkParameter) {
            return response()->json(['error' => 1001]);
        }
        // 再計算一次各個商品的數量*(資料庫裡的)單價最後的總額有沒有符合前端送來的amount
        (int)$count = 0;
        foreach ($this->detail as $detail) {
            $mealPrice = Meal::where("another_id", '=', $detail["another_id"])->where("restaurant_id", '=', $this->restaurant_id)->first()->price;
            $count = $count + (int)$detail['quantity'] * (int)$mealPrice;
        }
        if ($count != $this->amount) { {
                return response()->json(['error' => 3004]);
            }
        }
        // wallet.balance>amount(總額)
        $walletBalance = Wallet::where("user_id", "=", Auth::user()->id)->where('status', 1)->sum('balance');
        if ($walletBalance < $this->amount) {
            return response()->json(['error' => 3001]);
        }
        // 生成UUID
        $uuid = (string) Str::uuid();
        // 店家回傳成功收到訂單?
        $restaurantResponse = $restaurant->send_order(
            $this->user_name,
            $this->phone,
            $this->restaurant_id,
            $this->amount,
            $this->status,
            $this->remark,
            $this->pick_up_time,
            $this->created_time,
            $this->detail,
            $uuid
        );
        if ($restaurantResponse != 0) {
            return response()->json(['error' => $restaurantResponse]);
        }
        //         成功修改資料表orders、order_details、wallet_logs、wallets
        // orders
        $order = new Order;
        $order->user_id = Auth::user()->id;
        $order->restaurant_id = $this->restaurant_id;
        $order->name = $this->user_name;
        $order->another_id = $uuid;
        $order->choose_payment = "creidt";
        $order->amount = $this->amount;
        $order->status = 1;
        $order->remark = $this->remark;
        $order->pick_up_time = $this->pick_up_time;
        $order->created_at = $this->created_time;
        $order->save();

        // order_details
        foreach ($this->detail as $onemeal) {
            // var_dump($onemeal[ "meal_name"]);
            $order_detail = new OrderDetail;
            $order_detail->order_id = $order->id;
            $mealId = Meal::where("restaurant_id", "=", $this->restaurant_id)->where("another_id", "=", $onemeal["another_id"])->first()->id;
            $order_detail->meal_id = $mealId;
            $order_detail->meal_another_id = $onemeal['another_id'];
            $order_detail->price = $onemeal['price'];
            $order_detail->quantity = $onemeal['quantity'];
            $order_detail->amount = $onemeal['amount'];
            $order_detail->remark = $onemeal['meal_remark'];
            $order_detail->created_at = $this->created_time;
            $order_detail->save();
        }
        // wallet_logs
        $wallets = Wallet::where('user_id', "=", Auth::user()->id)->where('status', "=", 1)->orderBy("wallet_type", "desc")->get()->toArray();

        // 先扣非主錢包，從type大的開始減
        $totalAmount = $this->amount;
        foreach ($wallets as $wallet) {
            $wallet_logs = new WalletLog;
            $wallet_logs->user_id = Auth::user()->id;
            $wallet_logs->wallet_id = $wallet['id'];
            $wallet_logs->order_id = $order->id;
            if ($totalAmount > $wallet['balance']) {
                $wallet_logs->amount = $wallet['balance'];
                $wallet_logs->balance = 0;
            } else {
                $wallet_logs->amount = $totalAmount = $this->amount;
                $wallet_logs->balance = $wallet['balance'] - $totalAmount;
            }

            $wallet_logs->status = 1;
            $wallet_logs->remark = "";
            $wallet_logs->created_at = $this->created_time;
            $wallet_logs->save();

            $walletNew = Wallet::where("id", "=", $wallet['id'])->first();
            $walletNew->balance = $wallet_logs->balance;
            $walletNew->save();

            if ($totalAmount <= 0) {
                break;
            }
        }



        return response()->json(['error' => "0"]);
    }

    // 驗證器
    public function checkParameter()
    {
        $checkParameter = true;
        $validatorOrder1 = Validator::make([
            'user_name' => $this->user_name,
            'phone' => $this->phone,
            'restaurant_id' => $this->restaurant_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'remark' => $this->remark,
            'pick_up_time' => $this->pick_up_time,
            'created_time' => $this->created_time,
        ], [
            'user_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'restaurant_id' => 'required|integer|min:1|max:9223372036854775807',
            'amount' => 'required|integer|min:-2147483648|max:100000',
            'status' => 'required|integer|min:-128|max:127',
            'remark' => 'required|string|max:255',
            'pick_up_time' => 'required|string|max:255',
            'created_time' => 'required|string|max:255',
        ]);
        if ($validatorOrder1->fails()) {
            $checkParameter = false;
        }
        foreach ($this->detail as $detail) {
            $validatorOrder2 = Validator::make([
                'meal_name' => $detail['meal_name'],
                'price' => $detail['price'],
                'quantity' => $detail['quantity'],
                'amount' => $detail['amount'],
            ], [
                'meal_name' => 'required|string|max:255',
                'price' => 'required|integer|min:1|max:100000',
                'quantity' => 'required|integer|max:11',
                'amount' => 'required|integer|min:1|max:100000',

            ]);
            if ($validatorOrder2->fails()) {
                $checkParameter = false;
            }
        }

        return $checkParameter;
    }
}
