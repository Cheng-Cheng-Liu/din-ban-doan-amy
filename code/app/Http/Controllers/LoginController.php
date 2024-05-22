<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{


    public $email;
    private $password;


    public function __construct(Request $request)
    {
        $this->email = $request->input('email');
        $this->password = $request->input('password');
    }

    public function index(Request $request){
        // 檢查參數正確嗎?
        $checkParameter = $this->checkParameter();
        if ($checkParameter->fails()) {
            return response()->json(['error'=>1001]);
        }



        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $token = $user->createToken('front')->plainTextToken;
            return response()->json(['message' => 'Login successful','token' => $token]);
        } else {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }
    }


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
}
