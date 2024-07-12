<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class StatisticRestaurantOrderAmountHourly implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //key=餐廳ID+日期(年月日)
        $date = Carbon::now();
        $date->subHour();
        $formattedDate = $date->format('Ymd');
        $formattedDateDash = $date->format('Y-m-d H');
        $hour = $date->format('H');
        $sql = "
        SELECT restaurants.id as 'restaurant_id', IFNULL(total_amount, 0) as 'total_amount'
        FROM restaurants
        LEFT JOIN (
            SELECT restaurant_id, SUM(amount) as total_amount
            FROM orders
            WHERE orders.created_at BETWEEN ? AND ?
            AND orders.status=1
            GROUP BY restaurant_id
        ) A ON restaurants.id = A.restaurant_id
        where restaurants.status=1
    ";
        $start = $formattedDateDash . ':00:00';
        $stop = $formattedDateDash . ':59:59';
        $results = DB::select($sql, [$start, $stop]);
        // 有序排列，分數->數量，值->時間
        foreach ($results as $oneRestaurant) {
            $key = $oneRestaurant->restaurant_id . $formattedDate;
            Redis::zadd($key, $oneRestaurant->total_amount, $hour);
        }
    }
}
