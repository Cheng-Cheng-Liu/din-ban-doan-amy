<?php

namespace App\Services\Restaurants;

use App\Contracts\RestaurantInterface;
use App\Models\Restaurant;
use App\Models\Meal;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Http;
use App\Services\Restaurants\Librarys\RestaurantLibrary;

class SteakHome implements RestaurantInterface
{
    public function getMealsByApi()
    {
        // 有連線成功的話
        $response = Http::get(config('services.restaurant.steakhome') . '/api/menu/ls');
        if ($response->failed()) {
            $status = $response->status();
            Log::channel('getMeal')->info('steakHome_error' . $status);
            return $status;
        }

        $getMeal = $response->object();

        $restaurantId = Restaurant::where('service', '=', 'SteakHome')->get(['id'])->first()->id;
        $existMealId = Meal::where('restaurant_id', $restaurantId)
            ->get(['another_id'])->toArray();
        $anotherIds = array_column($existMealId, 'another_id');
        $newMealAnotherId = [];
        foreach ($getMeal->LS as $oneMeal) {
            $newMealAnotherId[] = $oneMeal->ID;
            if (!in_array((string)$oneMeal->ID, $anotherIds)) {
                $meal = new Meal();
                $meal->restaurant_id = $restaurantId;
                $meal->another_id = $oneMeal->ID;
                $meal->name = $oneMeal->NA;
                $meal->price = $oneMeal->PRC;
                $meal->status = 1;
                $meal->save();
            } else {
                $meal = Meal::where('restaurant_id', $restaurantId)
                    ->where('another_id', $oneMeal->ID)
                    ->update([
                        'name' => $oneMeal->NA,
                        'price' => $oneMeal->PRC,
                    ]);
            }
        }
        // 將舊菜單的status改成2
        foreach ($existMealId as $oneMeal) {
            if (!in_array((string)$oneMeal["another_id"], $newMealAnotherId)) {
                $meal = Meal::where('another_id', '=', $oneMeal["another_id"])->where('restaurant_id', $restaurantId)
                    ->update([
                        'status' => 2,
                    ]);
            }
        }

        $restaurantLibrary = new RestaurantLibrary;
        $restaurantLibrary->updateEnableMealsToRedis($restaurantId);

        return __('error.success');
    }

    public function sendOrder($data)
    {
        extract($data);

        $detailMeal = [];
        $i = 0;

        foreach ($detail as $meal) {
            $detailMeal[$i]['ID'] = $meal['another_id'];
            $detailMeal[$i]['NOTE'] = $meal['meal_remark'];
            $i++;
        }

        $data = [
            'OID' => $uuid,
            'NA' => $user_name,
            'PH_NUM' => $phone,
            'TOL_PRC' => $amount,
            'LS' => $detailMeal
        ];

        $serverOutput = Http::post(config('services.restaurant.steakhome') . '/api/mk/order', $data);

        if ($serverOutput->failed()) {
            $status = $serverOutput->status();
            Log::channel('sendOrder')->info('steakHome_error' . $status);
        }

        if ($serverOutput == '{"ERR":0}') {
            $response = 0;
        } else {
            $response = 3002;
        }

        return $response;
    }
}
