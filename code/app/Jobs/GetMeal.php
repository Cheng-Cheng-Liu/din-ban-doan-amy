<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Restaurant;
use App\Services\Restaurants\SteakHome;
use App\Services\Restaurants\Oishii;
use App\Services\Restaurants\Tasty;
use Illuminate\Support\Facades\Log;

class GetMeal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $restaurant = Restaurant::where('service', '!=', '')->get(['service'])->toArray();
        foreach ($restaurant as $oneRestaurant) {
            switch ($oneRestaurant['service']) {
                case 'SteakHome':
                    $restaurant = new SteakHome();
                    $restaurant->getMealsByApi();
                    break;
                case 'Oishii':
                    $restaurant = new Oishii();
                    $restaurant->getMealsByApi();
                    break;
                case 'Tasty':
                    $restaurant = new Tasty();
                    $restaurant->getMealsByApi();
                    break;
                default:
                    Log::channel('getMeal')->info('get_meal_job_error' . $oneRestaurant);
                    break;
            }
        }
    }
}
