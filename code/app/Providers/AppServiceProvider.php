<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Contracts\RestaurantInterface;
use App\Services\Restaurants\SteakHome;
use App\Services\Restaurants\Oishii;
use App\Services\Restaurants\Tasty;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(RestaurantInterface::class, function () {

            switch (Auth::guard('back')->user()->name) {
                case 'SteakHome':
                    return new SteakHome();
                    break;
                case 'Oishii':
                    return new Oishii();
                    break;
                case 'Tasty':
                    return new Tasty();
                    break;
                default:
                    echo "餐廳錯了";
                    break;
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
