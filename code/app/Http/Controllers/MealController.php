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
    function saveMeal(RestaurantInterface $restaurant)
    {
        $restaurant->getMeals();

        RestaurantLibrary::updateAllStatusOneMealsToRedis();

        return response()->json(['error' => __('error.success')]);
    }

    function getMeals(Request $request,$id){
        // 總筆數
        $total = Redis::connection('db2')->zcard('restaurant_id:'.$id);
        // 筆數
        $limit = $request->input('limit') ? $request->input('limit') : $total;
        // 第幾頁
        $offset = $request->input('offset') ? $request->input('offset') : 1;
        $page = CommonLibrary::page(['total' => $total, 'limit' => $limit, 'offset' => $offset]);
        $start = $page['start'];
        $stop = $page['stop'];
        // 依分數由小到大排序取出redis裡的資料
        $results = Redis::connection('db2')->zrange('restaurant_id:'.$id, $start, $stop);

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

    public function getBackMeals(Request $request,$id)
    {
        $meals = Restaurantlibrary::getAllBackMeals($id);
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
                    'id'=>$meal['id'],
					'name'=> $meal['name'],
					'price'=>$meal['price'],
					'another_id'=> $meal['another_id'],
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
        Meal::create($requests);
        // 更新redis
        RestaurantLibrary::updateAllStatusOneMealsToRedis();

        return response()->json(['error' => __('error.success')]);
    }

    public function putMeal(MealRequest $request, $id)
    {
        $requests = $request->all();
        // 更新資料庫
        Meal::find($id)->update($requests);

        // 更新redis
        Restaurantlibrary::updateAllStatusOneMealsToRedis();

        return response()->json(['error' => __('error.success')]);
    }


    public function deleteMeal($id){
        // 更新資料庫
        Meal::find($id)->delete();
        // 更新redis
        Restaurantlibrary::updateAllStatusOneMealsToRedis();

        return response()->json(['error' => __('error.success')]);

    }
}


