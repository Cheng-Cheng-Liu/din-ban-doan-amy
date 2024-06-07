<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Meal;
use App\Models\Wallet;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\Restaurants\SteakHome;
use App\Services\Restaurants\Oishii;
use App\Services\Restaurants\Tasty;
use App\Contracts\RestaurantInterface;

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
        $restaurantResponse=$restaurant->send_order(
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

        return response()->json(['error' => $restaurantResponse]);
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
