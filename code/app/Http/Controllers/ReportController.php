<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    //
    public function restaurantOrderAmount(Request $request)
    {
        // 檢查參數正確嗎?
        $validator = Validator::make([
            'date' => $request->input('date'),
            'limit' => $request->input('limit'),
            'offset' => $request->input('offset'),
        ], [
            'date' => 'string|nullable',
            'limit' => 'integer|nullable',
            'offset' => 'integer|nullable',
        ]);

        if ($validator->fails()) {

            return response()->json(['error' => 1001]);
        }

        //    預設昨天
        $dateCarbon = Carbon::now();
        $yesterday = $dateCarbon->subDay();
        $formattedDate = $yesterday->format('Ymd');
        $date = ($request->input('date')) ?? $formattedDate;
        $restaurants = Restaurant::where('status', '=', 1)->get()->toArray();
        $result = [];
        $i = 0;
        // 從redis取出所有啟用中餐廳的時間對應消費總額
        foreach ($restaurants as $restaurant) {
            $redisKey = $restaurant['id'] . $date;
            $value = Redis::zrange($redisKey, 0, -1, ['withscores' => true]);
            foreach ($value as $hr => $amount) {
                $result[$restaurant['id']][$hr] = $amount;
            }
            $i++;
        }

        if (empty($result)) {

            return response()->json(['error' => 5001]);
        }

        $total = $i;
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
        $limit = $total;
        $offset = 1;
        // 如果有arg就帶入limit跟offset
        $limit = $request->input('limit') ?? $limit;
        $offset = $request->input('offset') ?? $offset;
        $limit = ($limit > $total) ? $total : $limit;
        $offset = (($limit * $offset) > $total) ? ceil(($i + 1) / $total) : $offset;
        // 取出限制的筆數與頁數
        $keys = array_keys($sortResult);
        $limitResult = [];
        for ($i = ($limit * ($offset - 1)); $i < (($limit * $offset)); $i++) {
            $restaurantId = $keys[$i];
            $limitResult[] = $sortResult[$restaurantId];
        }
        // 組成list內容
        $list = [];
        foreach ($limitResult as $restaurantId => $statistics) {
            $statisticsArray = [];
            foreach ($statistics as $time => $amount) {
                $statisticsArray[] = ['time' => $time, 'amount' => $amount];
            }
            $list[] = [
                'id' => $restaurantId,
                'statistics' => $statisticsArray
            ];
        }

        $response = [
            'total' => $total,
            'list' => $list
        ];

        return $response;
    }


    public function statisticPersonalAccessTokenLogCountHourly(Request $request)
    {
        //    預設昨天
        $dateCarbon = Carbon::now();
        $formattedDate = $dateCarbon->subDay()->format('Ymd');
        $date = ($request->input('date')) ?? $formattedDate;
        $redisKey = 'StatisticPersonalAccessTokenLogCountHourly' . $date;
        $value = Redis::zrange($redisKey, 0, -1, ['withscores' => true]);
        // 從redis取出所有啟用中餐廳的時間對應消費總額
    }
}
