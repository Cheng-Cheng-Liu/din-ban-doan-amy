<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\MealController;
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


Route::get('test',[MealController::class, 'saveMeal'] );

