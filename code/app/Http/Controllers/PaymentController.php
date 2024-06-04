<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
        $now = $date->format('YmdHisu');
        $trade_no = "mypay" . $now;
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
            $rowString .= $one;
        }
        $rowCheck = "hash_key=61533ba5927296cd&" . $rowString . "&hash_iv=ffb5b7effb04eb95";
        // urlencode
        $urlencodeRow = $this->ecpayUrlEncode($rowCheck);
        $hashRow = hash_hmac("sha256", $urlencodeRow, env('SHA256_SECRET'));
        $check_mac_value = strtoupper($hashRow);
        return response()->json(['error' => $check_mac_value]);
    }
    function rechargeResult()
    {
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
