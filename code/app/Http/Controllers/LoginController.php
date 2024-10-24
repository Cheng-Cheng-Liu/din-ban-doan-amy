<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\PersonalAccessToken;
use Exception;

class LoginController extends Controller
{

    // 前台會員登入
    public function memberLogin(Request $request)
    {

        $email = $request->input('email');
        $password = $request->input('password');

        // 檢查參數正確嗎?
        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
        ], [
            'email' => 'required|string|email|max:255',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {

            return response()->json(['error' => __('error.invalidParameters')]);
        }

        // 比對mail、password是否一致?
        $credentials = $request->only('email', 'password');
        if (!$token = auth()->attempt($credentials)) {

            return response()->json(['error' => __('error.wrongAccountOrPassword')]);
        }

        /** @var User $user */
        $user = Auth::user();
        // 檢查email是不是已註冊完成
        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            return  response()->json(['error' => __('error.pleaseVerifiedEmail')]);
        }

        // 確認會員狀態是否啟用
        $status = Auth::user()->status;
        if ($status != 1) {

            return  response()->json(['error' => __('error.memberStatusProblem')]);
        }

        $payload = JWTAuth::setToken($token)->getPayload();
        $jti = $payload->get('jti');
        $id = Auth::user()->id;

        try {
            // 生成token並記錄在personal_access_tokens
            PersonalAccessToken::create([
                'tokenable_type' => 'App\Models\User',
                'tokenable_id' => $id,
                'name' => 'front',
                'token' => $jti,
            ]);
        } catch (Exception $e) {

            // 回傳錯誤訊息
            return response()->json(['error' => 1002]);
        }

        // 成功，回傳相關訊息
        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $token,
        ]);
    }

    // 後台會員登入
    public function adminLogin(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // 檢查參數正確嗎?
        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
        ], [
            'email' => 'required|string|email|max:255',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {

            return response()->json(['error' => __('error.invalidParameters')]);
        }

        $credentials = $request->only('email', 'password');
        if ($token = auth()->guard('back')->attempt($credentials)) {

            // 確認會員狀態是否啟用
            $status = auth()->guard('back')->user()->status;
            if ($status != 1) {

                return  response()->json(['error' => 'memberStatusProblem']);
            }

            $payload = JWTAuth::setToken($token)->getPayload();
            $jti = $payload->get('jti');
            $id = Auth::guard('back')->user()->id;

            try {
                // 生成token並記錄在personal_access_tokens
                PersonalAccessToken::create([
                    'tokenable_type' => 'App\Models\User',
                    'tokenable_id' => $id,
                    'name' => 'back',
                    'token' => $jti,
                ]);
            } catch (Exception $e) {

                // 回傳錯誤訊息
                return response()->json(['error' => 'databaseExecError']);
            }

            return response()->json([
                'message' => 'Logged in successfully',
                'token' => $token,
            ]);
        } else {

            return response()->json(['error' => __('error.wrongAccountOrPassword')]);
        }
    }

    // 前台會員登出
    public function memberLogout()
    {
        $user = Auth::user();
        $id = $user->id;
        // 刪除該users. id的所有token、redis中所有”取得會員的啟用中全部餐廳”的相關資料
        Redis::del('myrestaurant' . $id);
        PersonalAccessToken::where('tokenable_id', $id)->where('tokenable_type', 'App\Models\User')->delete();
        // jwt套件登出方法，寫入cache黑名單
        auth()->logout();

        return response()->json(['message' => 'Logged out successfully']);
    }

    // 後台會員登出
    public function backLogout()
    {
        $user = Auth::guard('back')->user();
        $id = $user->id;
        PersonalAccessToken::where('tokenable_id', $id)->where('tokenable_type', 'App\Models\Admin')->delete();
        // jwt套件登出方法，寫入cache黑名單
        auth()->guard('back')->logout();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
