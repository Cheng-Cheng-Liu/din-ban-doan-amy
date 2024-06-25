<?php

namespace App\Services\Restaurants\Librarys;

use Illuminate\Support\Facades\DB;

use App\Models\Restaurant;

class RestaurantLibrary
{

    public static function getAllStatusOneRestaurants()
    {
        $result = Restaurant::where('status', '=', 1)->orderBy('priority')->get()->toArray();
        return $result;
    }
    public static function getAllRestaurants()
    {
        $result = Restaurant::orderBy('priority')->get()->toArray();
        return $result;
    }
    public static function getAllStatusOneMemberRestaurants($id)
    {
        $result = DB::select("
        SELECT restaurants.id as 'id', restaurants.name as 'name', restaurants.phone as 'phone', restaurants.tag as 'tag',
        restaurants.opening_time as 'opening_time',
        restaurants.closing_time as 'closing_time',
        restaurants.rest_day as 'rest_day',
        restaurants.avg_score as 'avg_score',
        restaurants.total_comments_count as 'total_comments_count',
        CASE WHEN A.id IS NULL THEN FALSE ELSE TRUE END AS 'favorite'
        FROM restaurants LEFT JOIN (select * from favorites where user_id = ?)A ON restaurants.id = A.restaurant_id WHERE restaurants.status = 1
        ORDER BY restaurants.priority ASC, id ASC", [$id]);
        return $result;
    }
}
