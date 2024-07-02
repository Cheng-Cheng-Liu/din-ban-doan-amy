<?php

namespace App\Services\Restaurants;

use Illuminate\Http\Request;
use App\Models\Meal;
use App\Contracts\RestaurantInterface;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\Restaurants\Librarys\RestaurantLibrary;


class Oishii implements RestaurantInterface
{
    public function getMealsByApi()
    {
        $response = Http::get(config('services.restaurant.oishii') . '/api/menu/all');
        if ($response->failed()) {
            $status = $response->status();
            Log::channel('getMeal')->info('Oishii_error' . $status);
            return $status;
        }

        $getMeal = $response->object();
        $restaurantId = Restaurant::where('service', '=', 'Oishii')->get(['id'])->first()->id;
        $existMealId = Meal::where('restaurant_id', $restaurantId)
            ->get(['another_id'])->toArray();
        $anotherIds = array_column($existMealId, 'another_id');
        $newMealAnotherId = [];
        foreach ($getMeal->menu as $oneMeal) {
            $newMealAnotherId[] = $oneMeal->meal_id;
            if (!in_array((string)$oneMeal->meal_id, $anotherIds)) {
                $meal = new Meal();
                $meal->restaurant_id = $restaurantId;
                $meal->another_id = $oneMeal->meal_id;
                $meal->name = $oneMeal->meal_name;
                $meal->price = $oneMeal->price;
                $meal->status = 1;
                $meal->save();
            } else {
                $meal = Meal::where('restaurant_id', $restaurantId)
                    ->where('another_id', $oneMeal->meal_id)
                    ->update([
                        'name' => $oneMeal->meal_name,
                        'price' => $oneMeal->price,
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
            $detailMeal[$i]['meal_id'] = $meal['another_id'];
            $detailMeal[$i]['count'] = $meal['quantity'];
            $detailMeal[$i]['memo'] = $meal['meal_remark'];
            $i++;
        }
        $date = Carbon::parse($pick_up_time);
        $formattedDate = $date->format('Y-m-d\TH:i:sP');
        $data = [
            'id' => $uuid,
            'name' => $user_name,
            'phone_number' => $phone,
            'pickup_time' => $formattedDate,
            'total_price' => $amount,
            'orders' => $detailMeal
        ];
        $serverOutput = Http::post(config('services.restaurant.oishii') . '/api/notify/order', $data);

        if ($serverOutput->failed()) {
            $status = $serverOutput->status();
            Log::channel('sendOrder')->info('Oishii_error' . $status);
        }

        if ($serverOutput['error_code'] == 0) {
            $response = 0;
        } else {
            $response = 3002;
        }

        return $response;
    }
}
