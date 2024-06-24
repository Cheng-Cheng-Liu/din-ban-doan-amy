<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Restaurant;
use App\Services\Restaurants\Librarys\Restaurantlibrary;

class RestaurantController extends Controller
{
    //
    // public function get_member_restaurants(Request $request)
    // {
    //     $user = Auth::user();
    //     // $id=$user->id;
    //     $id = 17;
    //     $key = 'myrestaurant' . $id;
    //     $myRestaurantData = Redis::get($key);
    //     $myRestaurant = json_decode($myRestaurantData);
    //     if (!empty($request->all())) {
    //         $limit = $request->input("limit");
    //         $offset = $request->input("offset");
    //         $start = ($offset - 1) * $limit;
    //         $limitRestaurant = [];
    //         for ($i = $start; $i < $limit * $offset; $i++) {
    //             $limitRestaurant[] = $myRestaurant->list[$i];
    //         }
    //         $myRestaurantNew = [
    //             "total" => $myRestaurant->total,
    //             "list" => $limitRestaurant
    //         ];
    //         return $myRestaurantNew;
    //     } else {
    //         return $myRestaurant;
    //     }
    // }

    public function putRestaurant()
    {
        $restaurants=Restaurantlibrary::getAllStatusOneRestaurants();
        foreach($restaurants as $restaurant){
            $rId=str_pad((string)$restaurant['id'], 5, '0', STR_PAD_LEFT);
            $score=$restaurant['priority'].$rId;
            Redis::connection('db2')->zadd('all_status_one_restaurants',$score,'data');
        }

    }
}
