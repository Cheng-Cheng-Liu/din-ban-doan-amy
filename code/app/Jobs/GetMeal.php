<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Restaurant;
use App\Contracts\RestaurantInterface;
use App\Http\Controllers\MealController;
use Illuminate\Support\Facades\App;
use App\Services\Restaurants\SteakHome;
use App\Services\Restaurants\Oishii;
use App\Services\Restaurants\Tasty;

class GetMeal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $myRestaurant;
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
                    $restaurant->getMeals();
                    break;
                case 'Oishii':
                    $restaurant = new Oishii();
                    $restaurant->getMeals();
                    break;
                case 'Tasty':
                    $restaurant = new Tasty();
                    $restaurant->getMeals();
                    break;
                default:
                    echo $oneRestaurant;
                    break;
            }
        }
    }
}
