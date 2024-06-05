<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\CreditPayRecord;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public $amount;
    public function __construct(Request $request)
    {
        $this->amount = $request->input('amount');
    }
    function recharge()
    {
        // 檢查參數正確嗎?
        $checkParameter = $this->checkParameter();
        if ($checkParameter->fails()) {
            return response()->json(['error' => 1001]);
        }
        // 訂單編號
        $date = Carbon::now();
        $now = $date->format('YmdHis');
        $trade_no = "mypay" . (string)$now;
        // 生成檢查碼
        $amount = $this->amount;
        $choose_payment = "Credit";
        $encrypt_type = 1;
        $item_name = "product 1";
        $lang = "en";
        $merchant_id = 1;
        $merchant_trade_date = $date->format('Y/m/d H:i:s');
        $merchant_trade_no = $trade_no;
        $payment_type = "aio";
        $return_url = "http://localhost:8082/api/wallet/recharge/result";
        $trade_desc = "購買商品 1";
        $row = [
            "amount=" . $amount,
            "choose_payment=" . $choose_payment,
            "encrypt_type=" . $encrypt_type,
            "item_name=" . $item_name,
            "lang=" . $lang,
            "merchant_id=" . $merchant_id,
            "merchant_trade_date=" . $merchant_trade_date,
            "merchant_trade_no=" . $merchant_trade_no,
            "payment_type=" . $payment_type,
            "return_url=" . $return_url,
            "trade_desc=" . $trade_desc
        ];
        sort($row);
        $rowString = "";
        foreach ($row as $one) {
            $add = $one . "&";
            $rowString .= $add;
        }
        $rowCheck = "hash_key=61533ba5927296cd&" . $rowString . "hash_iv=ffb5b7effb04eb95";
        // urlencode
        $urlencodeRow = $this->ecpayUrlEncode($rowCheck);
        // hash256
        $hashRow = hash("sha256", $urlencodeRow);
        $check_mac_value = strtoupper($hashRow);
        // curl
        $data = [
            "amount" => $amount,
            "choose_payment" => $choose_payment,
            "encrypt_type" => $encrypt_type,
            "item_name" => $item_name,
            "lang" => $lang,
            "merchant_id" => $merchant_id,
            "merchant_trade_date" => $merchant_trade_date,
            "merchant_trade_no" => $merchant_trade_no,
            "payment_type" => $payment_type,
            "return_url" => $return_url,
            "trade_desc" => $trade_desc,
            "check_mac_value" => $check_mac_value
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://neil.xincity.xyz:9997/api/Cashier/AioCheckOut");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            http_build_query($data)
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        $position = strpos($server_output, "error");

        // 把資料寫入credit_pay_records
        if ($position == false) {
            $user=Auth::user();
            $creditPayRecord=new CreditPayRecord;
            $creditPayRecord->user_id=$user->id;
            $creditPayRecord->payment_type=$payment_type;
            $creditPayRecord->merchant_id=$merchant_id;
            $creditPayRecord->merchant_trade_no=$merchant_trade_no;
            $creditPayRecord->amount=$amount;
            $creditPayRecord->trade_desc=$trade_desc;
            $creditPayRecord->item_name=$item_name;
            $creditPayRecord->check_mac_value=$check_mac_value;
            $creditPayRecord->status=0;
            $creditPayRecord->remark="";
            $creditPayRecord->payment_date=$merchant_trade_date;
            $creditPayRecord->trade_date=$merchant_trade_date;
            if(!$creditPayRecord->save()){
                return response()->json(['error' => 1002]);
            }
        }{
            Log::channel('credit')->info('your_message');
        }


        return response()->json(['error' =>$server_output]);
    }


    function rechargeResult(Request $request)
    {
        $check_mac_value = $request->input('check_mac_value');
        echo $check_mac_value;
    }


    // 驗證器
    public function checkParameter()
    {
        $validator = Validator::make([
            'amount' => $this->amount,
        ], [
            'amount' => 'required|integer',
        ]);
        return $validator;
    }
    /**
     * URL 編碼
     *
     * @param  string $source
     * @return string
     */
    public static function ecpayUrlEncode($source)
    {
        $encoded = urlencode($source);
        $lower = strtolower($encoded);
        $dotNetFormat = self::toDotNetUrlEncode($lower);

        return $dotNetFormat;
    }

    /**
     * 轉換為 .net URL 編碼結果
     *
     * @param  string $source
     * @return string
     */
    public static function toDotNetUrlEncode($source)
    {
        $search = [
            '%2d',
            '%5f',
            '%2e',
            '%21',
            '%2a',
            '%28',
            '%29',
        ];
        $replace = [
            '-',
            '_',
            '.',
            '!',
            '*',
            '(',
            ')',
        ];
        $replaced = str_replace($search, $replace, $source);

        return $replaced;
    }
}
