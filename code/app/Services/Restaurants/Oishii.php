<?php

namespace App\Services\Restaurants;

use Illuminate\Http\Request;
use App\Models\Meal;
use App\Contracts\RestaurantInterface;
use App\Models\Restaurant;
use Carbon\Carbon;

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

        $curl = curl_init();





        $url = 'http://neil.xincity.xyz:9998/oishii/api/menu/all';
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

        foreach ($getMeal->menu as $oneMeal) {


            if (!in_array((string)$oneMeal->meal_id, $anotherIds)) {
                $meal = new Meal();
                $meal->restaurant_id = $restaurantId;
                $meal->another_id = $oneMeal->meal_id;
                $meal->name = $oneMeal->meal_name;
                $meal->price = $oneMeal->price;
                $meal->status = 1;
                $meal->save();
            } {
                $meal = Meal::where('restaurant_id', $restaurantId)
                    ->where('another_id', $oneMeal->meal_id)
                    ->update([
                        'name' => $oneMeal->meal_name,
                        'price' => $oneMeal->price,
                    ]);
            }
        }
    }
    public function send_order($user_name, $phone, $restaurant_id, $amount, $status, $remark, $pick_up_time, $created_time, $detail, $uuid)
    {
        // curl
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

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://neil.xincity.xyz:9998/oishii/");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            http_build_query($data)
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
        if($server_output==''){
            $response=0;
        }else{
            $response=3002;
        }
        return $response;
    }
}
