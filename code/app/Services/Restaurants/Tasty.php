<?php

namespace App\Services\Restaurants;

use App\Contracts\RestaurantInterface;
use App\Models\Meal;
use App\Models\Restaurant;

class Tasty implements RestaurantInterface
{


    public $id;
    public function __construct()
    {
        $response = Restaurant::where('service', '=', 'Tasty')->get(['id'])->first();
        $this->id = $response["id"];
    }
    public function get_meals()
    {

        $curl = curl_init();





        $url = 'http://neil.xincity.xyz:9998/tasty/api/menu';
        // $url = 'http://220.128.133.15/s1120214/api.php';

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
        ]);


        $response = curl_exec($curl);


        curl_close($curl);


        $getMeal = json_decode($response);
        $restaurantId = $this->id;
        $existMealId = Meal::where('restaurant_id', $restaurantId)
            ->get(['another_id'])->toArray();
        $anotherIds = array_column($existMealId, 'another_id');

        foreach ($getMeal->data->list as $oneMeal) {

            if ($oneMeal->enable) {
                if (!in_array((string)$oneMeal->id, $anotherIds)) {
                    $meal = new Meal();
                    $meal->restaurant_id = $restaurantId;
                    $meal->another_id = $oneMeal->id;
                    $meal->name = $oneMeal->name;
                    $meal->price = $oneMeal->price;
                    $meal->status = 1;
                    $meal->save();
                } {
                    $meal = Meal::where('restaurant_id', $restaurantId)
                        ->where('another_id', $oneMeal->id)
                        ->update([
                            'name' => $oneMeal->name,
                            'price' => $oneMeal->price,
                        ]);
                }
            }
        }
    }
    public function send_order()
    {
        return "";
    }
}
