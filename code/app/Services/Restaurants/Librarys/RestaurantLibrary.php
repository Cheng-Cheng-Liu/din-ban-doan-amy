<?php

namespace App\Services\Restaurants\Librarys;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Models\Restaurant;
use App\Models\Meal;

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

    public static function getAllStatusOneMeals()
    {
        $result = Meal::where('status', '=', 1)->orderBy('id')->get()->toArray();
        return $result;
    }

    public static function getAllBackMeals($id)
    {
        $result = Meal::where('restaurant_id', '=', $id)->orderBy('id')->get()->toArray();
        return $result;
    }

    public static function updateAllStatusOneRestaurantsToRedis()
    {
        // 先刪除舊資料
        Redis::connection('db2')->del('all_status_one_restaurants');
        // 再加入新資料
        $restaurants = RestaurantLibrary::getAllStatusOneRestaurants();
        foreach ($restaurants as $restaurant) {
            // score是priority+id，例如priority=1，id=1，score=100001
            $rId = str_pad((string)$restaurant['id'], 5, '0', STR_PAD_LEFT);
            $score = $restaurant['priority'] . $rId;
            $data = [
                'id' => $restaurant['id'],
                'name' => $restaurant['name'],
                'tag' => $restaurant['tag'],
                'phone' => $restaurant['phone'],
                'opening_time' => $restaurant['opening_time'],
                'closing_time' => $restaurant['closing_time'],
                'rest_day' => $restaurant['rest_day'],
                'avg_score' => $restaurant['avg_score'],
                'total_comments_count' => $restaurant['total_comments_count'],
            ];
            $data = json_encode($data);
            // data以json儲存
            Redis::connection('db2')->zadd('all_status_one_restaurants', $score, $data);
        }
    }

    public static function updateAllStatusOneMealsToRedis()
    {
        // 先刪除舊資料
        $keys = Redis::keys('restaurant_id:*');
        if (!empty($keys)) {
            Redis::del($keys);
        }
        // 再加入新資料
        $meals = RestaurantLibrary::getAllStatusOneMeals();
        foreach ($meals as $meal) {
            $data = [
                'id' => $meal['id'],
                'name' => $meal['name'],
                'price' => $meal['price'],
                'another_id' => $meal['another_id'],
            ];
            $jsonData=json_encode($data);
            // data以json儲存
            Redis::connection('db2')->zadd('restaurant_id:' . $meal['restaurant_id'], $meal['id'], $jsonData);
        }
    }
}
