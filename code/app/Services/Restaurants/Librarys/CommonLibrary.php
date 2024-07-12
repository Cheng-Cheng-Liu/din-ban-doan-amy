<?php

namespace App\Services\Restaurants\Librarys;


class CommonLibrary
{
    /**
 * Calculate the start and end positions for pagination.
 *
 * Input parameters:
 * @param array $data An array containing the following keys:
 *   - limit (int): The number of records per page
 *   - offset (int): The current page number, starting from 1
 *   - total (int): The total number of records
 *
 * Return value:
 * @return array An array containing the following keys:
 *   - start (int): The start position of the records, starting from 0
 *   - stop (int): The end position of the records
 */
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
