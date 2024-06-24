<?php
namespace App\Services\Restaurants\Librarys;

use App\Models\Restaurant;

class Restaurantlibrary {

    public static function getAllStatusOneRestaurants(){
        $result=Restaurant::where('status','=',1)->orderBy('priority')->get()->toArray();
        return $result;
    }

}