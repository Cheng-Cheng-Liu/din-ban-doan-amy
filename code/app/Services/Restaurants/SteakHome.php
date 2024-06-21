<?php

namespace App\Services\Restaurants;

use App\Contracts\RestaurantInterface;
use App\Models\Restaurant;
use App\Models\Meal;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Http;

class SteakHome implements RestaurantInterface
{
    public function getMeals()
    {
        // 有連線成功的話
        $response = Http::get(config('services.restaurant.steakhome') . '/api/menu/ls');
        if ($response->failed()) {
            $json = $response->throw()->json();
            Log::channel('getMeal')->info('steakHome_error' . $json);
        }

        $getMeal = $response->object();

        $restaurantId = Restaurant::where('service', '=', 'SteakHome')->get(['id'])->first()->id;
        $existMealId = Meal::where('restaurant_id', $restaurantId)
            ->get(['another_id'])->toArray();
        $anotherIds = array_column($existMealId, 'another_id');

        foreach ($getMeal->LS as $oneMeal) {
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
            $json = $serverOutput->throw()->json();
            Log::channel('getMeal')->info('steakHome_error' . $json);
        }

        if ($serverOutput == '{"ERR":0}') {
            $response = 0;
        } else {
            $response = 3002;
        }

        return $response;
    }
}
