<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Mail;
use App\Mail\HelloMail;
use Illuminate\Support\Facades\Log;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [RestaurantController::class,'get_member_restaurants']);

// 寄認證信
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
// 收認證信
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user=User::find($id);
    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => '請關閉此頁面並重新登入'], 403);
    }
    $now = now();
    $user->email_verified_at = $now;
    $user->save();
    return "請關閉此頁面並重新登入";
})->name('verification.verify');
// 重寄認證信
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/mail', function(){
    Mail::to('juliet6124amy@gmail.com')->send(new HelloMail());
});


Route::get('/pay', function(){
    return view("pay");
});
Route::get('/clean', function(){
    return view("clean");
});
Route::post('wallet/recharge/result', function(Request $request){
    Log::channel('credit')->info($request);
});