<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\Admin;
use App\Models\Personal_access_token;

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



        $credentials = $request->only('email', 'password');
        if ($token = auth()->attempt($credentials)) {
            $id = Auth::user()->id;
            $time = now();
            $personal_access_token = new Personal_access_token();
            $personal_access_token->tokenable_type = "App\Models\User";
            $personal_access_token->tokenable_id = $id;
            $personal_access_token->name = "front";
            $personal_access_token->token = $token;
            $personal_access_token->created_at = $time;
            $personal_access_token->save;
            return $this->respondWithToken($token);
        } else {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }
    }
    // 後台會員登入
    public function adminLogin(Request $request)
    {

        $credentials = $request->only('email', 'password');
        if ($token = auth()->guard('admin')->attempt($credentials)) {
            return $this->respondWithToken($token);
        } else {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }
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
}
