<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Contracts\RestaurantInterface;
use App\Services\Restaurants\Librarys\RestaurantLibrary;
use App\Services\Restaurants\Librarys\CommonLibrary;
use App\Models\Meal;
use App\Http\Requests\MealRequest;

class MealController extends Controller
{
    private static $redis;

    function __construct()
    {
        self::$redis = Redis::connection('restaurant');
    }

    function saveMeal(RestaurantInterface $restaurant)
    {
        $result = $restaurant->getMealsByApi();

        return ['error' => $result];
    }

    function getMeals(Request $request)
    {
        // 餐廳id
        $restaurantId = $request->input('restaurant_id');
        // 總筆數$i
        $total = self::$redis->zcard('restaurant_id:' . $restaurantId);
        // 筆數
        $limit = $request->input('limit') ? $request->input('limit') : $total;
        // 第幾頁
        $offset = $request->input('offset') ? $request->input('offset') : 1;
        $page = CommonLibrary::page(['total' => $total, 'limit' => $limit, 'offset' => $offset]);
        $start = $page['start'];
        $stop = $page['stop'];
        // 依分數由小到大排序取出redis裡的資料
        $results = self::$redis->zrange('restaurant_id:' . $restaurantId, $start, $stop);

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

    public function getBackMeals(Request $request)
    {
        $restaurantId = $request->input('restaurant_id');
        $meals = Restaurantlibrary::getAllBackMeals($restaurantId);
        $total = count($meals);
        // 筆數
        $limit = $request->input('limit') ? $request->input('limit') : $total;
        // 第幾頁
        $offset = $request->input('offset') ? $request->input('offset') : 1;

        $page = CommonLibrary::page(['total' => $total, 'limit' => $limit, 'offset' => $offset]);
        $start = $page['start'];
        $stop = $page['stop'];
        $list = [];

        foreach ($meals as $index => $meal) {

            if ($index >= $start && $index <= $stop) {
                $list[] = [
                    'id' => $meal['id'],
                    'name' => $meal['name'],
                    'price' => $meal['price'],
                    'another_id' => $meal['another_id'],
                ];
            }
        }

        $data = [
            'total' => $total,
            'list' => $list,
        ];

        return response()->json($data);
    }

    public function addMeal(MealRequest $request)
    {
        $requests = $request->all();
        // 更新資料庫
        $meal = Meal::create($requests);
        // 更新redis
        $restaurantlibrary=new Restaurantlibrary;
        $restaurantlibrary->updateEnableMealsToRedis($meal->restaurant_id);


        return response()->json(['error' => __('error.success')]);
    }

    public function putMeal(MealRequest $request)
    {
        $restuurantId = $request->input('restaurant_id');
        $anotherId=$request->input('another_id');
        $requests = $request->all();
        // 更新資料庫
        Meal::where("restaurant_id","=",$restuurantId)->where("another_id","=",$anotherId)->update($requests);

        // 更新redis
        $restaurantlibrary=new Restaurantlibrary;
        $restaurantlibrary->updateEnableMealsToRedis($restuurantId);

        return response()->json(['error' => __('error.success')]);
    }

    public function deleteMeal(Request $request)
    {
        $restuurantId = $request->input('restaurant_id');
        $anotherId=$request->input('another_id');
        // 更新資料庫
        Meal::where("restaurant_id","=",$restuurantId)->where("another_id","=",$anotherId)->delete();
        // 更新redis
        $restaurantlibrary=new Restaurantlibrary;
        $restaurantlibrary->updateEnableMealsToRedis($restuurantId);

        return response()->json(['error' => __('error.success')]);
    }
}
