<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Wallet;


class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $email = $request->input('email');
        $name = $request->input('name');
        $nickname = $request->input('nickname');
        $password = $request->input('password');
        $phone = $request->input('phone');

        // 檢查參數正確嗎?
        $validator = Validator::make([
            'name' => $name,
            'nickname' => $nickname,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
        ], [
            'name' => 'required|string|max:255',
            'nickname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {

            return response()->json(['error' => __('error.invalidParameters')]);
        }

        $user = User::where('email', '=', $email)->first();
        // 已經有這個email
        if (!is_null($user)) {
            // 檢查email是不是已註冊完成
            $hasVerifiedEmail = $user->hasVerifiedEmail();
            if ($hasVerifiedEmail) {

                return  response()->json(['error' => __('error.emailAlreadyVerified')]);
            }
            // 更新資料庫name、password欄位
            $user->update([
                'name' => $name,
                'nickname' => $nickname,
                'password' => bcrypt($password),
                'phone' => $phone,
            ]);
            // 重寄認證信
            $user->sendEmailVerificationNotification();

            // 返回最後成功訊息
            return  response()->json(['error' => __('error.pleaseVerifiedEmail')]);
        }

        // 沒有這個帳號的話
        // 插入name、email、password、status=1(啟用:1，停權:2)、nickname、phone、roles=[‘member’]到users資料表
        $user = User::create([
            'email' => $email,
            'name' => $name,
            'nickname' => $nickname,
            'password' => bcrypt($password),
            'phone' => $phone
        ]);
        // Register event寄送認證信
        event(new Registered($user));

        // 做一個主錢包
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
            'status' => 1,
            'wallet_type' => 1,
            'remark' => ''
        ]);

        // 返回最後成功訊息
        return  response()->json(['error' => __('error.pleaseVerifiedEmail')]);
    }
}
