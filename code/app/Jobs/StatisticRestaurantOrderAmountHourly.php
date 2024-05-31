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

        $formattedDate = $date->format('Ymd');
        $formattedDateDash = $date->format('Y-m-d');
        $hour_now = $date->format('H');
        $hour = $hour_now - 1;

        $start = $formattedDateDash . " " . $hour . ":00:00";
        $stop = $formattedDateDash . " " . $hour . ":59:59";
        $Order_amount_sum_hourly = Order::select('restaurant_id', DB::raw('SUM(amount) as total_amount'))
            ->whereBetween('created_at', [$start, $stop])
            ->groupBy('restaurant_id')
            ->get();

        foreach ($Order_amount_sum_hourly as $oneRestaurant) {
            $key = $oneRestaurant['restaurant_id'] . $formattedDate;
            Redis::rpush($key, $oneRestaurant['total_amount']);
        }
    }
}
