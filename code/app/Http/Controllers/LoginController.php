<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\Admin;
use App\Models\Personal_access_token;
use Exception;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

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
            $id = Auth::user()->id;
            try {
                // 生成token並記錄在personal_access_tokens
                $personal_access_token = new Personal_access_token;
                $personal_access_token->tokenable_type = "App\Models\User";
                $personal_access_token->tokenable_id = $id;
                $personal_access_token->name = "front";
                $personal_access_token->token = 'token';
                $personal_access_token->save();
            } catch (Exception $e) {
                // 回傳錯誤訊息
                return response()->json(['error' => 1002]);
            }
            // 生成要給”取得會員的啟用中全部餐廳”這支api的redis資料
            $myRestaurantData = DB::select('
            SELECT restaurants.id as "id", restaurants.name as "name", restaurants.phone as "phone",
            restaurants.opening_time as "opening_time",
            restaurants.closing_time as "closing_time",
            restaurants.rest_day as "rest_day",
            restaurants.avg_score as "avg_score",
            restaurants.total_comments_count as "total_comments_count",
            CASE WHEN A.id IS NULL THEN FALSE ELSE TRUE END AS "favorite"
            FROM restaurants LEFT JOIN (select * from favorites where user_id = ?)A ON restaurants.id = A.restaurant_id WHERE restaurants.status = 1
            ORDER BY restaurants.priority ASC, id ASC', [$id]);
            $total = count($myRestaurantData);

            $myRestaurant = [
                "total" => $total,
                "list" => $myRestaurantData

            ];
            $jsonData =json_encode($myRestaurant);
            Redis::set('myrestaurant' . $id, $jsonData);


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
        if ($token = auth()->guard('admin')->attempt($credentials)) {
            return $this->respondWithToken($token);
        } else {
            return response()->json(['error' => 2002]);
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
