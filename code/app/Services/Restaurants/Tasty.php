<?php

namespace App\Services\Restaurants;

use App\Contracts\RestaurantInterface;
use App\Models\Meal;
use App\Models\Restaurant;
use Carbon\Carbon;

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
                }else {
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
    public function send_order($user_name, $phone, $restaurant_id, $amount, $status, $remark, $pick_up_time, $created_time, $detail, $uuid)
    {
        // curl
        $detailMeal = [];
        $i = 0;
        foreach ($detail as $meal) {
            $detailMeal[$i]["id"] = $meal['another_id'];
            $detailMeal[$i]["count"] = $meal['quantity'];
            $detailMeal[$i]["memo"] = $meal['meal_remark'];
            $i++;
        }
        $date = Carbon::parse($pick_up_time);
        $formattedDate = $date->format('Y-m-d\TH:i:sP');
        $data = [
            "order_id" => $uuid,
            "name" =>$user_name,
            "phone_number" => $phone,
            "pickup_time" => $formattedDate,
            "total_price" => $amount,
            "list" => $detailMeal
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://neil.xincity.xyz:9998/tasty/api/order");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            http_build_query($data)
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
        if($server_output=='{"success":true,"error_code":0}'){
            $response=0;
        }else{
            $response=3002;
        }
        return $response;
    }
}
