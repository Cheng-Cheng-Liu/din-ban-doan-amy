<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Auth;
use App\Models\Personal_access_token;
use App\Models\PersonalAccessTokenLog;
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
Route::post('register',[RegisterController::class,'index']);
Route::post('login',[LoginController::class,'memberLogin']);
Route::post('login_back',[LoginController::class,'adminLogin']);

Route::prefix('member')->middleware(['auth:member'])->group(function () {
    Route::post('/logout',[LoginController::class,'memberLogout']);
    Route::get('/restaurants', [RestaurantController::class,'get_member_restaurants']);

});
Route::prefix('back')->middleware(['auth:back'])->group(function () {
    Route::post('/logout',[LoginController::class,'backLogout']);


});

Route::get('test',function (){

    $personal_access_token_log_count_hourly = PersonalAccessTokenLog::whereBetween('login_time', ['2024-05-31 10:00:00', '2024-05-31 10:59:59'])->count();
    return $personal_access_token_log_count_hourly;
});