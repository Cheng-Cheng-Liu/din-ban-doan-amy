<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Restaurant;

class ReportController extends Controller
{
    //
    public function restaurantOrderAmount(Request $request)
    {
        // 從redis取出所有啟用中餐廳的時間對應消費總額
        $date = $request->input('date');
        $restaurants = Restaurant::where("status", "=", 1)->get()->toArray();
        $result = [];
        $i = 0;
        foreach ($restaurants as $restaurant) {

            $redisKey = $restaurant['id'] . $date;
            $value = Redis::zrange($redisKey, 0, -1, ['withscores' => true]);



            foreach ($value as $hr => $amount) {
                $result[$restaurant['id']][$hr] = $amount;
            }





            $i++;
        }
        $total= $i;
        // 將取出的值按照時間重新排序
        $sortResult = [];
        foreach ($result as $key => $row) {
            $newRow = [];

            for ($i = 0; $i < 24; $i++) {
                if (isset($row[$i])) {
                    $newRow[$i] = $row[$i];
                } else {
                    continue;
                }
            }
            $sortResult[$key] = $newRow;
        }
        // 預設顯示全部資料在第一頁，筆數=總數，頁面=1
        $limit = $total ;
        $offset = 1;
        // 如果有arg就帶入limit跟offset
        $limit = $request->input('limit') ?? $limit;
        $offset= $request->input('offset')?? $offset;
        $limit=($limit > $total) ? $total : $limit;
        $offset=(($limit * $offset) > $total) ? ceil(($i + 1) / $total) : $offset;
        // 取出限制的筆數與頁數
        $keys = array_keys($sortResult);
        $limitResult = [];
        for ($i = ($limit * ($offset - 1)); $i < (($limit * $offset)); $i++) {
            $restaurantId=$keys[$i];
            $limitResult[$restaurantId] = $sortResult[$restaurantId];
        }
        // 組成list內容
        $list=[];
        foreach($limitResult as $restaurantId => $statistics){
            $list[]=[
                "id"=>$restaurantId,
                "statistics"=>$statistics
            ];
        }

        $response=[
            "total"=>$total,
            "list"=>$list

        ];
        return $response;
    }
}
