<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


class RestaurantController extends Controller
{
    //
    public function get_member_restaurants(Request $request){
        $user = Auth::user();
        // $id=$user->id;
        $id=17;
        $key='myrestaurant' . $id;
        $myRestaurantData=Redis::get($key);
        $myRestaurant=json_decode($myRestaurantData);
        if(!empty($request->all())){
            $limit=$request->input("limit");
            $offset=$request->input("offset");
            $start=($offset-1)*$limit;
            $limitRestaurant=[];
            for($i=$start;$i< $limit*$offset;$i++){
                $limitRestaurant[]=$myRestaurant->list[$i];
            }
            $myRestaurantNew=[
                "total" => $myRestaurant->total,
                "list" => $limitRestaurant
            ];
            return $myRestaurantNew;
        }else{
            return $myRestaurant;
        }
    }
}
