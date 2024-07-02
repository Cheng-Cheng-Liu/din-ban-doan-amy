<?php

namespace App\Services\Restaurants;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Meal;
use App\Models\Restaurant;
use Carbon\Carbon;
use App\Services\Restaurants\Librarys\RestaurantLibrary;
use App\Contracts\RestaurantInterface;

class Tasty implements RestaurantInterface
{
    public function getMealsByApi()
    {
        $response = Http::get(config('services.restaurant.tasty') . '/api/menu');
        if ($response->failed()) {
            $status = $response->status();
            Log::channel('getMeal')->info('Tasty_error' . $status);
            return $status;
        }

        $getMeal = $response->object();
        $restaurantId = Restaurant::where('service', '=', 'Tasty')->get(['id'])->first()->id;
        $existMealId = Meal::where('restaurant_id', $restaurantId)
            ->get(['another_id'])->toArray();
        $anotherIds = array_column($existMealId, 'another_id');
        $newMealAnotherId = [];
        foreach ($getMeal->data->list as $oneMeal) {

            Log::channel('getMeal')->info('Tasty_id' . $oneMeal->id);
            if ($oneMeal->enable) {
                if (!in_array((string)$oneMeal->id, $anotherIds)) {
                    $meal = new Meal();
                    $meal->restaurant_id = $restaurantId;
                    $meal->another_id = $oneMeal->id;
                    $meal->name = $oneMeal->name;
                    $meal->price = $oneMeal->price;
                    $meal->status = 1;
                    $meal->save();
                } else {
                    $meal = Meal::where('restaurant_id', $restaurantId)
                        ->where('another_id', $oneMeal->id)
                        ->update([
                            'name' => $oneMeal->name,
                            'price' => $oneMeal->price,
                        ]);
                }

                $newMealAnotherId[] = $oneMeal->id;
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
        // curl
        $detailMeal = [];
        $i = 0;
        foreach ($detail as $meal) {
            $detailMeal[$i]['id'] = $meal['another_id'];
            $detailMeal[$i]['count'] = $meal['quantity'];
            $detailMeal[$i]['memo'] = $meal['meal_remark'];
            $i++;
        }
        $date = Carbon::parse($pick_up_time);
        $formattedDate = $date->format('Y-m-d\TH:i:sP');
        $data = [
            'order_id' => $uuid,
            'name' => $user_name,
            'phone_number' => $phone,
            'pickup_time' => $formattedDate,
            'total_price' => $amount,
            'list' => $detailMeal
        ];

        $serverOutput = Http::post(config('services.restaurant.tasty') . '/api/order', $data);

        if ($serverOutput->failed()) {
            $status = $serverOutput->status();
            Log::channel('sendOrder')->info('Tasty_error' . $status);
        }

        if ($serverOutput == '{"success":true,"error_code":0}') {
            $response = 0;
        } else {
            $response = 3002;
        }
        return $response;
    }
}
