<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Auth;
use App\Models\WalletLog;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

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
    Route::get('/user', function(){
        $user=Auth::user();
        echo $user->id;
    });
    Route::post('/orders', [OrderController::class, 'create_order']);
});
Route::prefix('back')->middleware(['auth:back'])->group(function () {
    Route::post('/logout', [LoginController::class, 'backLogout']);
});


Route::get('/saveMeal', [MealController::class, 'saveMeal']);

Route::post('test', function(){
    $wallet_last_balance = WalletLog::where("user_id", "=", 1)->where("status", "=", 1)->where("wallet_type", "=", 1)->first();
                if ($wallet_last_balance == null) {
                    $wallet_last_balance_balance = 0;
                } else {
                    $wallet_last_balance_balance = $wallet_last_balance->balance;
                }

});