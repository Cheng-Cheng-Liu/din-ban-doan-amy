<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Mail;
use App\Mail\HelloMail;
use App\Jobs\StatisticPersonalAccessTokenLogCountHourly;
use App\Jobs\StatisticRestaurantOrderAmountHourly;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->call(function () {
        //     // 在调度任务中发送邮件
        //     Mail::to('juliet6124amy@gmail.com')->send(new HelloMail());
        // })->everyMinute();
        $schedule->job(new StatisticPersonalAccessTokenLogCountHourly,'reports','redis')->hourly();
        $schedule->job(new StatisticRestaurantOrderAmountHourly,'reports','redis')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
