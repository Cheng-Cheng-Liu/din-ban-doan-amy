<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Auth;
use App\Models\Personal_access_token;

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
    Route::post('/logout', function () {
        $user = Auth::user();
        $id=$user->id;
        Personal_access_token::where('tokenable_id', $id)->delete();
        return response()->json(['message' => 'Logged out successfully']);
    });
    Route::get('/restaurants', [RestaurantController::class,'get_member_restaurants']);

});
