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


Route::get('test', function(){

    return response()->json(['error' =>"test"]);
});

Route::get('o', function(){


// Set the URL to request
$url = "http://localhost:8082/api/test";


//初始化
$curl = curl_init();
//设置抓取的url
curl_setopt($curl, CURLOPT_URL, $url);
//设置头文件的信息作为数据流输出
curl_setopt($curl, CURLOPT_HEADER, 1);
//设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
     //执行命令
    $data = curl_exec($curl);
     //关闭URL请求
    curl_close($curl);
     //显示获得的数据
    print_r($data);
});