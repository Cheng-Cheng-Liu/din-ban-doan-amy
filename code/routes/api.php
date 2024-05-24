<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Auth;

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
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::prefix('member')->middleware(['auth:sanctum','verified'])->group(function () {
//     Route::get('/test', function () {
//         return 'this is member';
//     });
// });

Route::prefix('member')->middleware(['auth:member'])->group(function () {
    Route::get('/test', function () {
        return 'this is member';
    });
    Route::post('/logout', function () {
        $user = Auth::user();
        $user->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    });
});
