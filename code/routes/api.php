<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use App\Models\WalletLog;
use App\Models\Order;
use App\Models\Admin;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\CheckMacValue;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Services\Restaurants\Librarys\RestaurantLibrary;

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

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'memberLogin']);
Route::post('/login_back', [LoginController::class, 'adminLogin']);
// 金流
Route::post('/wallet/recharge/result', [PaymentController::class, 'rechargeResult']);
Route::get('/restaurants', [RestaurantController::class, 'getRestaurants']);
Route::get('/restaurants/menu',  [MealController::class, 'getMeals']);

Route::prefix('member')->middleware(['auth:member'])->group(function () {
    Route::post('/logout', [LoginController::class, 'memberLogout']);
    Route::get('/restaurants', [RestaurantController::class, 'get_member_restaurants']);
    // 金流
    Route::post('/wallets/recharge', [PaymentController::class, 'recharge']);
    Route::post('/orders', [OrderController::class, 'create_order']);
    Route::get('/restaurants', [RestaurantController::class, 'getMemberRestaurants']);
    Route::post('/test', function () {
        $user = Auth::user();
        echo $user->id;
        auth()->logout();
        $user2 = Auth::user();
        echo $user2->id;
    });
    Route::post('/test2', function (Request $request) {
        $token = $request->bearerToken();
        $payload = JWTAuth::parseToken()->getPayload($token);
        return $payload->get('jti');
    });
});
Route::prefix('back')->middleware(['auth:back','ip'])->group(function () {
    Route::post('/logout', [LoginController::class, 'backLogout']);
    Route::prefix('/report')->middleware(['auth:back'])->group(function () {
        Route::post('/members', [ReportController::class, 'restaurantOrderAmount']);
        Route::post('/statisticPersonalAccessTokenLogCountHourly', [ReportController::class, 'statisticPersonalAccessTokenLogCountHourly']);
    });
    Route::get('/restaurants', [RestaurantController::class, 'getBackRestaurants']);
    Route::post('/restaurants', [RestaurantController::class, 'addRestaurant']);
    Route::put('/restaurants', [RestaurantController::class, 'putRestaurant']);
    Route::delete('/restaurants', [RestaurantController::class, 'deleteRestaurant']);
    Route::get('/saveMeal', [MealController::class, 'saveMeal']);
    Route::get('/restaurants/menu', [MealController::class, 'getBackMeals']);
    Route::post('/restaurants/menu', [MealController::class, 'addMeal']);
    Route::put('/restaurants/menu', [MealController::class, 'putMeal']);
    Route::delete('/restaurants/menu', [MealController::class, 'deleteMeal']);
    Route::post('/test2', function (Request $request) {
        $token = $request->bearerToken();
        $payload = JWTAuth::parseToken()->getPayload($token);
        return $payload->get('jti');
    });
});

// 示範用api，設定ip進去memcached
Route::post('/example/setMyIpFor1000Minutes',  function (Request $req) {
    $ip = $req->ip();
    Cache::store('memcached')->put($ip, 'value', '1000');
    return $ip;
});

