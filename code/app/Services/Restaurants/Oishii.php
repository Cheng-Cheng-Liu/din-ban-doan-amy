<?php

namespace App\Services\Restaurants;

use Illuminate\Http\Request;
use App\Models\Meal;
use App\Contracts\RestaurantInterface;

class Oishii implements RestaurantInterface
{
    public $id = 3;

    public function get_meals()
    {

        $curl = curl_init();





        // $url = 'http://neil.xincity.xyz:9998/api/menu/all';
        $url = 'http://220.128.133.15/s1120214/api.php';

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
        ]);


        $response = curl_exec($curl);


        curl_close($curl);


        $getMeal = json_decode($response);
        $restaurantId=$this->id;
        $existMealId=Meal::where('restaurant_id', $restaurantId)
        ->get(['another_id'])->toArray();
        $anotherIds = array_column($existMealId, 'another_id');

        foreach($getMeal->menu as $oneMeal){


            if(!in_array((string)$oneMeal->meal_id,$anotherIds)){
                $meal= new Meal();
                $meal->restaurant_id = $restaurantId;
                $meal->another_id= $oneMeal->meal_id;
                $meal->name=$oneMeal->meal_name;
                $meal->price=$oneMeal->price;
                $meal->status=1;
                $meal->save();

            }{
                $meal = Meal::where('restaurant_id', $restaurantId)
            ->where('another_id', $oneMeal->meal_id)
            ->update([
                'name' => $oneMeal->meal_name,
                'price' => $oneMeal->price,
            ]);
            }
        }
    }
    public function send_order()
    {
        return "";
    }
}
