<?php

namespace App\Services\Restaurants\Librarys;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

use App\Models\Restaurant;
use App\Models\Meal;

class RestaurantLibrary
{
    private static $redis;

    function __construct()
    {
        self::$redis = Redis::connection('restaurant');
    }

    public static function getAllEnableRestaurants()
    {
        $result = Restaurant::where('status', '=', 1)->orderBy('priority')->get()->toArray();

        return $result;
    }

    public static function getAllRestaurants()
    {
        $result = Restaurant::orderBy('priority')->get()->toArray();
        return $result;
    }

    public static function getAllEnableMemberRestaurants($id)
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

    public static function getEnableMealsByRestaurantId($id)
    {
        $result = Meal::where('status', '=', 1)->where('restaurant_id', '=', $id)->orderBy('id')->get()->toArray();
        return $result;
    }

    public static function getAllBackMeals($id)
    {
        $result = Meal::where('restaurant_id', '=', $id)->orderBy('id')->get()->toArray();
        return $result;
    }

    public static function updateAllEnableRestaurantsToRedis()
    {
        // 先刪除舊資料
        $key = self::$redis->keys('all_status_one_restaurants');
        if (!empty($key)) {
            self::$redis->del($key);
        }

        self::$redis->del('all_status_one_restaurants');
        // 再加入新資料
        $restaurants = RestaurantLibrary::getAllEnableRestaurants();
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
            self::$redis->zadd('all_status_one_restaurants', $score, $data);
        }
    }
    /**
     * update a restaurant's enable meals to redis
     *
     * Input parameters:
     * @param int An restaurant id
     *
     *
     */
    public static function updateEnableMealsToRedis(int $id)
    {
        // 先刪除舊資料
        $key = self::$redis->keys('restaurant_id:' . $id);
        if (!empty($key)) {
            self::$redis->del($key);
        }

        // 再加入新資料
        $meals = RestaurantLibrary::getEnableMealsByRestaurantId($id);
        foreach ($meals as $meal) {
            $data = [
                'id' => $meal['id'],
                'name' => $meal['name'],
                'price' => $meal['price'],
                'another_id' => $meal['another_id'],
            ];
            $jsonData = json_encode($data);
            // data以json儲存
            self::$redis->zadd('restaurant_id:' . $meal['restaurant_id'], $meal['id'], $jsonData);
        }
    }
}
