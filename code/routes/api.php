<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Restaurant;
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

Route::prefix('member')->middleware(['auth:member'])->group(function () {
    Route::post('/logout', [LoginController::class, 'memberLogout']);
    Route::get('/restaurants', [RestaurantController::class, 'get_member_restaurants']);
    // 金流
    Route::post('/wallets/recharge', [PaymentController::class, 'recharge']);

});
Route::prefix('back')->middleware(['auth:back'])->group(function () {
    Route::post('/logout', [LoginController::class, 'backLogout']);
});


Route::get('/saveMeal', [MealController::class, 'saveMeal']);


Route::get('test', function(){
    $row=["avb5645","aa235","vhjk5","ab541"];
    sort($row);
    print_r($row);
});