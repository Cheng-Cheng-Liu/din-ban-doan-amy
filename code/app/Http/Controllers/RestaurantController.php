<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Restaurant;
use App\Services\Restaurants\Librarys\RestaurantLibrary;
use App\Services\Restaurants\Librarys\CommonLibrary;
use App\Http\Requests\RestaurantRequest;
use Illuminate\Support\Facades\Cache;

class RestaurantController extends Controller
{
    private static $redis;

    function __construct()
    {
        self::$redis = Redis::connection('restaurant');
    }

    public function getRestaurants(Request $request)
    {
        // 總筆數
        $total = self::$redis->zcard('all_status_one_restaurants');
        // 筆數
        $limit = $request->input('limit') ? $request->input('limit') : $total;
        // 第幾頁
        $offset = $request->input('offset') ? $request->input('offset') : 1;
        $page = CommonLibrary::page(['total' => $total, 'limit' => $limit, 'offset' => $offset]);
        $start = $page['start'];
        $stop = $page['stop'];
        // 依分數由小到大排序取出redis裡的資料
        $results = self::$redis->zrange('all_status_one_restaurants', $start, $stop);
        $list = [];
        foreach ($results as $result) {
            $docodeResult = json_decode($result);
            $list[] = $docodeResult;
        }
        $data = [
            'total' => $total,
            'list' => $list,
        ];

        return response()->json($data);
    }

    public function getRestaurant(Request $request)
    {
        $restaurantId = $request->input('restaurant_id');
        $jsonData = Cache::store('memcached')->get('restaurant:' . $restaurantId);
        $data = json_decode($jsonData);
        return $data;
    }

    public function getBackRestaurants(Request $request)
    {
        $restaurants = Restaurantlibrary::getAllRestaurants();
        $total = count($restaurants);
        // 筆數
        $limit = $request->input('limit') ? $request->input('limit') : $total;
        // 第幾頁
        $offset = $request->input('offset') ? $request->input('offset') : 1;

        $page = CommonLibrary::page(['total' => $total, 'limit' => $limit, 'offset' => $offset]);
        $start = $page['start'];
        $stop = $page['stop'];
        $list = [];

        foreach ($restaurants as $index => $restaurant) {

            if ($index >= $start && $index <= $stop) {
                $list[] = [
                    'id' => $restaurant['id'],
                    'name' => $restaurant['name'],
                    'tag' => $restaurant['tag'],
                    'phone' => $restaurant['phone'],
                    'opening_time' => $restaurant['opening_time'],
                    'closing_time' => $restaurant['closing_time'],
                    'rest_day' => $restaurant['rest_day'],
                    'avg_score' => $restaurant['avg_score'],
                    'total_comments_count' => $restaurant['total_comments_count'],
                    'status' => $restaurant['status'],
                    'priority' => $restaurant['priority'],
                ];
            }
        }

        $data = [
            'total' => $total,
            'list' => $list,
        ];

        return response()->json($data);
    }

    public function getMemberRestaurants(Request $request)
    {
        $restaurants = Restaurantlibrary::getAllEnableMemberRestaurants(Auth::user()->id);
        $total = count($restaurants);
        // 筆數
        $limit = $request->input('limit') ? $request->input('limit') : $total;
        // 第幾頁
        $offset = $request->input('offset') ? $request->input('offset') : 1;

        $page = CommonLibrary::page(['total' => $total, 'limit' => $limit, 'offset' => $offset]);
        $start = $page['start'];
        $stop = $page['stop'];
        $list = [];

        foreach ($restaurants as $index => $restaurant) {

            if ($index >= $start && $index <= $stop) {
                $list[] = [
                    'id' => $restaurant->id,
                    'name' => $restaurant->name,
                    'tag' => $restaurant->tag,
                    'phone' => $restaurant->phone,
                    'opening_time' => $restaurant->opening_time,
                    'closing_time' => $restaurant->closing_time,
                    'rest_day' => $restaurant->rest_day,
                    'avg_score' => $restaurant->avg_score,
                    'total_comments_count' => $restaurant->total_comments_count,
                    'favorite' => $restaurant->favorite,
                ];
            }
        }

        $data = [
            'total' => $total,
            'list' => $list,
        ];

        return response()->json($data);
    }

    public function addRestaurant(RestaurantRequest $request)
    {
        $name = $request->input('name');
        $tag = $request->input('tag');
        $phone = $request->input('phone');
        $openingTime = $request->input('opening_time');
        $closingTime = $request->input('closing_time');
        $restDay = $request->input('rest_day');
        $status = $request->input('status');
        $priority = $request->input('priority');
        // 補齊其他值
        $avgScore = 0;
        $totalCommentsCount = 0;
        // 更新資料庫
        $restaurant = Restaurant::create([
            'name' => $name,
            'tag' => $tag,
            'phone' => $phone,
            'opening_time' => $openingTime,
            'closing_time' => $closingTime,
            'rest_day' => $restDay,
            'status' => $status,
            'priority' => $priority,
            'avg_score' => $avgScore,
            'total_comments_count' => $totalCommentsCount,
        ]);

        // 更新快取全部餐廳資料
        $restaurantlibrary= new Restaurantlibrary;
        $restaurantlibrary->updateAllEnableRestaurantsToRedis();

        // 更新快取個別餐廳資料
        Restaurantlibrary::updateEachEnableRestaurantsToMemcached([
            'id' => $restaurant->id,
            'name' => $name,
            'tag' => $tag,
            'phone' => $phone,
            'opening_time' => $openingTime,
            'closing_time' => $closingTime,
            'rest_day' => $restDay,
            'avg_score' => $avgScore,
            'total_comments_count' => $totalCommentsCount,
        ]);

        return response()->json(['error' => __('error.success')]);
    }

    public function putRestaurant(RestaurantRequest $request)
    {

        $name = $request->input('name');
        $tag = $request->input('tag');
        $phone = $request->input('phone');
        $openingTime = $request->input('opening_time');
        $closingTime = $request->input('closing_time');
        $restDay = $request->input('rest_day');
        $status = $request->input('status');
        $priority = $request->input('priority');
        $restaurantId = $request->input('restaurant_id');

        // 更新資料庫
        Restaurant::find($restaurantId)->update([
            'name' => $name,
            'tag' => $tag,
            'phone' => $phone,
            'opening_time' => $openingTime,
            'closing_time' => $closingTime,
            'rest_day' => $restDay,
            'status' => $status,
            'priority' => $priority,
        ]);

        // 更新快取全部餐廳資料
        $restaurantlibrary= new Restaurantlibrary;
        $restaurantlibrary->updateAllEnableRestaurantsToRedis();

        // 更新快取個別餐廳資料
        $restaurant=Restaurant::find($restaurantId);
        Restaurantlibrary::updateEachEnableRestaurantsToMemcached([
            'id' => $restaurantId,
            'name' => $name,
            'tag' => $tag,
            'phone' => $phone,
            'opening_time' => $openingTime,
            'closing_time' => $closingTime,
            'rest_day' => $restDay,
            'avg_score' => $restaurant->avg_score,
            'total_comments_count' => $restaurant->total_comments_count,
        ]);

        return response()->json(['error' => __('error.success')]);
    }


    public function deleteRestaurant(Request $request)
    {
        // 餐廳id
        $restaurantId = $request->input('restaurant_id');

        // 更新資料庫
        Restaurant::find($restaurantId)->delete();

        // 更新快取全部餐廳資料
        $restaurantlibrary= new Restaurantlibrary;
        $restaurantlibrary->updateAllEnableRestaurantsToRedis();

        // 更新快取個別餐廳資料
        Cache::forget('restaurant:' . $restaurantId);

        return response()->json(['error' => __('error.success')]);
    }

    public function addRestaurantsToCache()
    {
        $restaurants = RestaurantLibrary::getAllEnableRestaurants();
        foreach ($restaurants as $restaurant) {
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
            $jsonData = json_encode($data);
            // 將全部餐廳個別加入cache
            Cache::store('memcached')->forever('restaurant:' . $restaurant['id'], $jsonData);
        }
    }
}
