<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\GetMeal;
use App\Jobs\StatisticPersonalAccessTokenLogCountHourly;
use App\Jobs\StatisticRestaurantOrderAmountHourly;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // 統計每小時會員登入報表
        // $schedule->job(new StatisticPersonalAccessTokenLogCountHourly,'reports','redis')->hourly();
        // (測試用)統計每分鐘會員登入報表
        $schedule->job(new StatisticPersonalAccessTokenLogCountHourly,'reports','redis')->everyMinute();

        // 統計每小時各家餐廳的訂單總額度
        // $schedule->job(new StatisticRestaurantOrderAmountHourly, 'reports', 'redis')->hourly();
        // (測試用)統計每分鐘各家餐廳的訂單總額度
        $schedule->job(new StatisticRestaurantOrderAmountHourly, 'reports', 'redis')->everyMinute();

        // 每日自動更新餐點
        // $schedule->job(new GetMeal())->daily();
        // (測試用)每分鐘自動更新餐點
        // $schedule->job(new GetMeal())->everyMinute();


    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
