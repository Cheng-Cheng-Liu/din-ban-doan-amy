<?php

namespace App\Services\Restaurants;

use App\Contracts\RestaurantInterface;
use App\Models\Restaurant;
use App\Models\Meal;



class SteakHome implements RestaurantInterface
{



    public $id;
    public function __construct()
    {
        $response = Restaurant::where('service', '=', 'SteakHome')->get(['id'])->first();
        $this->id = $response["id"];
    }

    public function get_meals()
    {

        $curl = curl_init();





        $url = 'http://neil.xincity.xyz:9998/steak_home/api/menu/ls';
        // $url = 'http://220.128.133.15/s1120214/api2.php';

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

        foreach ($getMeal->LS as $oneMeal) {


            if (!in_array((string)$oneMeal->ID, $anotherIds)) {
                $meal = new Meal();
                $meal->restaurant_id = $restaurantId;
                $meal->another_id = $oneMeal->ID;
                $meal->name = $oneMeal->NA;
                $meal->price = $oneMeal->PRC;
                $meal->status = 1;
                $meal->save();
            } {
                $meal = Meal::where('restaurant_id', $restaurantId)
                    ->where('another_id', $oneMeal->ID)
                    ->update([
                        'name' => $oneMeal->NA,
                        'price' => $oneMeal->PRC,
                    ]);
            }
        }
    }
    public function send_order()
    {
        return "";
    }
}
