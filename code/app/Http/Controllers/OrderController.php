<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Meal;

class OrderController extends Controller
{
    public $user_name;
    public $phone;
    public $restaurant_id;
    public $another_id;
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
        $this->another_id = $request->input("another_id");
        $this->amount = $request->input("amount");
        $this->status = $request->input("status");
        $this->remark = $request->input("remark");
        $this->pick_up_time = $request->input("pick_up_time");
        $this->created_time = $request->input("created_time");
        $this->detail = $request->input("detail");
    }
    public function create_order()
    {
        // 檢查參數正確嗎?
        $checkParameter = $this->checkParameter();
        if (!$checkParameter) {
            return response()->json(['error' => 1001]);
        }
        // 再計算一次各個商品的數量*(資料庫裡的)單價最後的總額有沒有符合前端送來的amount
        foreach($this->detail as $detail){
            $meal=Meal::where("another_id",'=',$detail["another_id"])->first();
            $detail['quantity']*$meal->price=$this->amount;
        }
    }

    // 驗證器
    public function checkParameter()
    {
        $checkParameter = true;
        $validatorOrder1 = Validator::make([
            'user_name' => $this->user_name,
            'phone' => $this->phone,
            'restaurant_id' => $this->restaurant_id,
            'another_id' => $this->another_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'remark' => $this->remark,
            'pick_up_time' => $this->pick_up_time,
            'created_time' => $this->created_time,
        ], [
            'user_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'restaurant_id' => 'required|integer|min:1|max:9223372036854775807',
            'another_id' => 'required|string|max:255',
            'amount' => 'required|integer|min:-2147483648|max:11',
            'status' => 'required|integer|min:-128|max:127',
            'remark' => 'required|string|max:255',
            'pick_up_time' => 'required|string|max:255',
            'created_time' => 'required|string|max:255',
        ]);
        if ($validatorOrder1->fails()) {
            $checkParameter = false;
        }
        foreach($this->detail as $detail){
            $validatorOrder2= Validator::make([
                'meal_name' => $detail['meal_name'],
                'another_id' =>$detail['another_id'],
                'price' => $detail['price'],
                'quantity' => $detail['quantity'],
                'amount' => $detail['amount'],
            ], [
                'meal_name' => 'required|string|max:255',
                'another_id' => 'required|string|max:255',
                'price' => 'required|integer|min:1|max:11',
                'quantity' => 'required|string|max:11',
                'amount' => 'required|integer|min:1|max:11',

            ]);
            if ($validatorOrder2->fails()) {
                $checkParameter = false;
            }
        }

        return $checkParameter;
    }
}
