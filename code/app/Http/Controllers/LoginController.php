<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\Admin;
use App\Models\PersonalAccessToken;
use Exception;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class LoginController extends Controller
{


    public $email;
    private $password;


    public function __construct(Request $request)
    {
        $this->email = $request->input('email');
        $this->password = $request->input('password');
    }
    // 前台會員登入
    public function memberLogin(Request $request)
    {
        // 檢查參數正確嗎?
        $checkParameter = $this->checkParameter();
        if ($checkParameter->fails()) {
            return response()->json(['error' => 1001]);
        }


        // 比對mail、password是否一致?
        $credentials = $request->only('email', 'password');
        if ($token = auth()->attempt($credentials)) {
            // 檢查email是不是已註冊完成
            $checkEmailVertifiedAtNotNull = $this->checkEmailVertifiedAtNotNull();

            if (!$checkEmailVertifiedAtNotNull) {
                Auth::user()->sendEmailVerificationNotification();
                return  response()->json(['error' => 2003]);
            }

            // 確認會員狀態是否啟用
            $status = Auth::user()->status;
            if ($status != 1) {
                return  response()->json(['error' => 2004]);
            }
            $string = $token;
            $parts = explode(".", $string);
            $lastPart = end($parts);

            $id = Auth::user()->id;
            try {
                // 生成token並記錄在personal_access_tokens
                $personal_access_token = new PersonalAccessToken;
                $personal_access_token->tokenable_type = "App\Models\User";
                $personal_access_token->tokenable_id = $id;
                $personal_access_token->name = "front";
                $personal_access_token->token = $lastPart;
                $personal_access_token->save();
            } catch (Exception $e) {
                // 回傳錯誤訊息
                return response()->json(['error' => 1002]);
            }


            // 成功，回傳相關訊息
            return $this->respondWithToken($token);
        } else {
            return response()->json(['error' => 2002]);
        }
    }
    // 後台會員登入
    public function adminLogin(Request $request)
    {

        $credentials = $request->only('email', 'password');
        if ($token = auth()->guard('web_admin')->attempt($credentials)) {
            // 確認會員狀態是否啟用
            $status = auth()->guard('web_admin')->user()->status;
            if ($status != 1) {
                return  response()->json(['error' => 2004]);
            }

            $id = auth()->guard('web_admin')->user()->id;
            try {
                // 生成token並記錄在personal_access_tokens
                $personal_access_token = new PersonalAccessToken;
                $personal_access_token->tokenable_type = "App\Models\Admin";
                $personal_access_token->tokenable_id = $id;
                $personal_access_token->name = "back";
                $personal_access_token->token = 'token';
                $personal_access_token->save();
            } catch (Exception $e) {
                // 回傳錯誤訊息
                return response()->json(['error' => 1002]);
            }
            return $this->respondWithToken($token);
        } else {
            return response()->json(['error' => 2002]);
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
        return response()->json(['message' => 'Logged out successfully']);
    }
    // 後台會員登出
    public function backLogout()
    {
        $user = Auth::user();
        $id = $user->id;
        PersonalAccessToken::where('tokenable_id', $id)->where('tokenable_type', 'App\Models\Admin')->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
    // 驗證器
    public function checkParameter()
    {
        $validator = Validator::make([
            'email' => $this->email,
            'password' => $this->password,
        ], [
            'email' => 'required|string|email|max:255',
            'password' => ['required', Password::min(6)],
        ]);
        return $validator;
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            "message" => "登入成功",
            "token" => $token,
        ]);
    }


    public function checkEmailVertifiedAtNotNull()
    {
        $user = User::where('email', $this->email)
            ->whereNotNull('email_verified_at')
            ->first();
        return $user;
    }
}
