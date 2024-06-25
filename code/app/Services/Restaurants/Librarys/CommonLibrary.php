<?php

namespace App\Services\Restaurants\Librarys;


class CommonLibrary
{
    // start從零開始
    // Input limit offset total
    // output start stop
    public static function page($data)
    {
        $limit = $data['limit'];
        $offset = $data['offset'];
        $total = $data['total'];
        // 處理如果筆數大於總數
        if ($limit > $total) {
            $limit = $total;
            $offset = 1;
        }

        // start是從第幾個成員開始(從0開始)
        $start = $limit * ($offset - 1);
        $stop = $start + $limit - 1;

        // 超過一律顯示最後一頁
        if ($stop > $total) {
            $lastPage = $total / $limit;
            $mod = $total % $limit;
            $start = $limit * ($lastPage - 1) + 1;
            $stop = $start + $mod - 1;
        }

        $result = [
            'start' => $start,
            'stop' => $stop,
        ];

        return $result;
    }
}
