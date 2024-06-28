<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use App\Models\PersonalAccessTokenLog;
use Carbon\Carbon;



class StatisticPersonalAccessTokenLogCountHourly implements ShouldQueue
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
        $date = Carbon::now();
        $date->subHour();
        $formattedDate = $date->format('Ymd');
        $formattedDateDash = $date->format('Y-m-d H');
        $hour = $date->format('H');
        $start = $formattedDateDash . ':00:00';
        $stop = $formattedDateDash . ':59:59';



        $personal_access_token_log_count_hourly = PersonalAccessTokenLog::whereBetween('login_time', [$start, $stop])->count();
        Redis::zadd('StatisticPersonalAccessTokenLogCountHourly' . $formattedDate, $personal_access_token_log_count_hourly, $hour);
    }
}
