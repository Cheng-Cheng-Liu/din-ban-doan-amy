<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public $name;
    public $nickname;
    public $email;
    private $password;
    public $phone;

    public function __construct(Request $request)
    {
        $this->email = $request->input('email');
        $this->name = $request->input('name');
        $this->nickname = $request->input('nickname');
        $this->password = $request->input('password');
        $this->phone = $request->input('phone');
    }
    public function index()
    {
        // 檢查參數正確嗎?
        $checkParameter = $this->checkParameter();
        if ($checkParameter->fails()) {
            return response()->json(['error'=>1001]);
        }
        // 檢查email是不是已註冊完成
        $checkEmailVertifiedAtNotNull = $this->checkEmailVertifiedAtNotNull();

        if ($checkEmailVertifiedAtNotNull) {
            return  response()->json(['error' => 2001]);

        }
        // users資料表有沒有這個email
        $checkEmailExist = $this->checkEmailExist();
        if ($checkEmailExist) {
            $user = $checkEmailExist;
            // 更新資料庫name、password欄位
            $user->update([
                'name' => $this->name,
                'nickname' => $this-> nickname,
                'password' => bcrypt($this->password),
                'phone' => $this->phone,
            ]);
            // 重寄認證信
            $user->sendEmailVerificationNotification();
            return  response()->json(['error' => 2003]);
        } else {
            $this->CreateUser();
            return  response()->json(['error' => 2003]);
        }
    }

    public function checkParameter()
    {
        $validator = Validator::make([
            'name' => $this->name,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
        ], [
            'name' => 'required|string|max:255',
            'nickname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'password' => ['required', Password::min(6)],
        ]);
        return $validator;

    }

    public function checkEmailVertifiedAtNotNull()
    {
        $user = User::where('email', $this->email)
            ->whereNotNull('email_verified_at')
            ->first();
        return $user;
    }

    public function checkEmailExist()
    {
        $user = User::where('email', $this->email)
            ->first();
        return $user;
    }



    public function CreateUser()
    {
        $user = User::create([
            'email' => $this->email,
            'name' => $this->name,
            'nickname' => $this->nickname,
            'password' => bcrypt($this->password),
            'phone' => $this->phone,
        ]);
        event(new Registered($user));

    }
}
