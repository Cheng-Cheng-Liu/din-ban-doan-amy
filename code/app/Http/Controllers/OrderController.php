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
use App\Jobs\ProcessOrderJob;

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
        // foreach ($detail as $oneDetail) {
        //     $mealPrice = Meal::where('another_id', '=', $oneDetail['another_id'])->where('restaurant_id', '=', $restaurantId)->first()->price;
        //     $count = $count + (int)$oneDetail['quantity'] * (int)$mealPrice;
        // }
        // if ($count != $amount) { {

        //         return ['error' => __('error.totalAmountWrong')];
        //     }
        // }

        // cache lock start
        $lock = Cache::lock('foo' . $userName, 10);

        if ($lock->get()) {

            $data = $request->all();
            $data['user_id'] = Auth::user()->id;
            $data['restaurant'] = $restaurant;
            ProcessOrderJob::dispatch($data);

            return ['error' => __('error.success')];
        } else {
            // 還鎖著
            return ['error' => __('error.doNotRepeatSentRequest')];
        }
        // cache lock end


    }
}
