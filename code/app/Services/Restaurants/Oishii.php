<?php

namespace App\Services\Restaurants;

use Illuminate\Http\Request;
use App\Models\Meal;
use App\Contracts\RestaurantInterface;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class Oishii implements RestaurantInterface
{
    public $id;
    public function __construct()
    {
        $response = Restaurant::where('service', '=', 'Oishii')->get(['id'])->first();
        $this->id = $response["id"];
    }


    public function get_meals()
    {

        $response = Http::get(config('services.restaurant.oishii')."/api/menu/all");
        $getMeal = $response->object();
        $restaurantId = $this->id;
        $existMealId = Meal::where('restaurant_id', $restaurantId)
            ->get(['another_id'])->toArray();
        $anotherIds = array_column($existMealId, 'another_id');

        foreach ($getMeal->menu as $oneMeal) {


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
    }
    public function send_order($data)
    {
        extract($data);

        $detailMeal = [];
        $i = 0;
        foreach ($detail as $meal) {
            $detailMeal[$i]["meal_id"] = $meal['another_id'];
            $detailMeal[$i]["count"] = $meal['quantity'];
            $detailMeal[$i]["memo"] = $meal['meal_remark'];
            $i++;
        }
        $date = Carbon::parse($pick_up_time);
        $formattedDate = $date->format('Y-m-d\TH:i:sP');



        $data = [
            "id" => $uuid,
            "name" => $user_name,
            "phone_number" => $phone,
            "pickup_time" => $formattedDate,
            "total_price" => $amount,
            "orders" => $detailMeal
        ];
        $server_output = Http::post(config('services.restaurant.oishii') . '/api/notify/order', $data);

        if ($server_output->failed()) {
            $json = $server_output->throw()->json();
            Log::channel('getMeal')->info("steakHome_error" . $json);
        }
        if ($server_output == '{"ERR":0}') {
            $response = 0;
        } else {
            $response = 3002;
        }

        return $response;
    }
}
