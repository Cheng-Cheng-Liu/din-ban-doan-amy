<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [RegisterController::class, 'index']);
Route::post('login', [LoginController::class, 'memberLogin']);
Route::post('login_back', [LoginController::class, 'adminLogin']);

Route::prefix('member')->middleware(['auth:member'])->group(function () {
    Route::post('/logout', [LoginController::class, 'memberLogout']);
    Route::get('/restaurants', [RestaurantController::class, 'get_member_restaurants']);
});
Route::prefix('back')->middleware(['auth:back'])->group(function () {
    Route::post('/logout', [LoginController::class, 'backLogout']);
});

Route::get('test', function () {

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
        echo $key;
        echo $oneRestaurant['total_amount'];
        echo "<hr>";
    }
});
