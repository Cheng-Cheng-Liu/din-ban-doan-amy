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
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\CheckMacValue;
use Illuminate\Support\Facades\Cache;
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

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'memberLogin']);
Route::post('login_back', [LoginController::class, 'adminLogin']);
// 金流
Route::post('wallet/recharge/result', [PaymentController::class, 'rechargeResult']);
// Route::post('wallet/recharge/result', function(Request $request){
//     Log::channel('credit')->info($request);
// });


Route::prefix('member')->middleware(['auth:member'])->group(function () {
    Route::post('/logout', [LoginController::class, 'memberLogout']);
    Route::get('/restaurants', [RestaurantController::class, 'get_member_restaurants']);
    // 金流
    Route::post('/wallets/recharge', [PaymentController::class, 'recharge']);
    Route::post('/orders', [OrderController::class, 'create_order']);
    Route::post('/test', function () {
        $user = Auth::user();
        echo $user->id;
        auth()->logout();
        $user2 = Auth::user();
        echo $user2->id;
    });
    Route::post('/test2', function () {
        $wallets = Wallet::where('user_id', '=', Auth::user()->id)->where('status', '=', 1)->orderBy('wallet_type', 'desc')->get()->toArray();
        var_dump($wallets);
        foreach ($wallets as $wallet) {
            echo $wallet['id'];
        }
    });
});
Route::prefix('back')->middleware(['auth:back'])->group(function () {
    Route::post('/logout', [LoginController::class, 'backLogout']);
    Route::prefix('/report')->middleware(['auth:back'])->group(function () {
        Route::post('/members', [ReportController::class, 'restaurantOrderAmount']);
        Route::post('/statisticPersonalAccessTokenLogCountHourly', [ReportController::class, 'statisticPersonalAccessTokenLogCountHourly']);
    });
});


Route::get('/saveMeal', [MealController::class, 'saveMeal']);


Route::post('/test', function(){

    $wallets = Wallet::where('user_id', '=', Auth::user()->id)->where('status', '=', 1)->orderBy('wallet_type', 'desc')->get()->toArray();
    var_dump($wallets);
});