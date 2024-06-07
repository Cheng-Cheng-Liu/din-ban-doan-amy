<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\CreditPayRecord;
use App\Models\Wallet;
use App\Models\WalletLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        $return_url = "http://192.168.83.28:9999/api/wallet/recharge/result";
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
            $user = Auth::user();
            $creditPayRecord = new CreditPayRecord;
            $creditPayRecord->user_id = $user->id;
            $creditPayRecord->payment_type = $payment_type;
            $creditPayRecord->merchant_id = $merchant_id;
            $creditPayRecord->merchant_trade_no = $merchant_trade_no;
            $creditPayRecord->amount = $amount;
            $creditPayRecord->trade_desc = $trade_desc;
            $creditPayRecord->item_name = $item_name;
            $creditPayRecord->check_mac_value = $check_mac_value;
            $creditPayRecord->status = 0;
            $creditPayRecord->remark = "";
            $creditPayRecord->payment_date = $merchant_trade_date;
            $creditPayRecord->trade_date = $merchant_trade_date;
            if (!$creditPayRecord->save()) {
                return response()->json(['error' => 1002]);
            }
        } else {
            Log::channel('credit')->info("server_output" . $server_output);
        }


        return response()->json(['error' => $server_output]);
    }


    function rechargeResult(Request $request)
    {
        Log::channel('credit')->info($request);
        $check_mac_value = $request->input('check_mac_value');
        // Log::channel('credit')->info("check_mac_value".$check_mac_value);
        // check_mac_value一致
        $my_check_mac_value = $this->checkMacValue($request->all());
        if ($my_check_mac_value==$check_mac_value) {
            $user = CreditPayRecord::where("merchant_trade_no", '=', $request->input('merchant_trade_no'))->first()->user_id;
            // credit_pay_record.status改成trade_status的值
            $creditPayRecordRtn = CreditPayRecord::where("merchant_trade_no", '=', $request->input('merchant_trade_no'))->first();
            $creditPayRecordRtn->status = $request->input('rtn_code');
            $creditPayRecordRtn->save();

            if ($request->input('rtn_code')) {
                // 如果該會員沒錢包先創一個錢包
                $wallet = Wallet::where("user_id", "=", $user)->where("status", "=", 1)->where("wallet_type", "=", 1)->first();
                if ($wallet == null) {
                    $addWallet = new Wallet;
                    $addWallet->user_id = $user;
                    $addWallet->balance = 0;
                    $addWallet->status = 1;
                    $addWallet->wallet_type = 1;
                    $addWallet->remark = "";
                    $addWallet->save();
                }
                Log::channel('credit')->info("rtn_code" .  $request->input('rtn_code'));
                // 確認這筆成功的信用卡紀錄是否已寫入過wallet_logs
                // $value=WalletLog::where("check_mac_value", '=', $check_mac_value)->first()->credit_pay_record_id;
                $credit_pay_record_id = CreditPayRecord::where("merchant_trade_no", '=', $request->input('merchant_trade_no'))->first()->id;

                $walletLogRepeat = WalletLog::where("user_id", "=", $user)->where("credit_pay_record_id", "=", $credit_pay_record_id)->first();
                
                if ($walletLogRepeat) {
                    Log::channel('credit')->info("repeat credit_pay_record_id" . $walletLogRepeat->credit_pay_record_id);

                    return response()->json(['received' => 1]);
                }
                // 增加wallet_logs紀錄
                $walletLog = new WalletLog;
                $walletLog->credit_pay_record_id =CreditPayRecord::where("merchant_trade_no", '=', $request->input('merchant_trade_no'))->first()->id;
                $walletLog->user_id = $user;
                $wallet2 = Wallet::where("user_id", "=", $user)->where("status", "=", 1)->where("wallet_type", "=", 1)->first();
                $walletLog->wallet_id = $wallet2->id;
                $walletLog->order_id = null;
                $walletLog->amount = $request->input('amount');
                $wallet_last_balance = WalletLog::where("user_id", "=", $user)->where("status", "=", 1)->orderBy('id', 'desc')->first();
                if ($wallet_last_balance == null) {
                    $wallet_last_balance_balance = 0;
                } else {
                    $wallet_last_balance_balance = $wallet_last_balance->balance;
                }
                Log::channel('credit')->info("server_output" .  $wallet_last_balance_balance);
                $walletLog->balance = (int)$wallet_last_balance_balance + (int)$request->input('amount');
                $balance = $walletLog->balance;
                $walletLog->status = 1;
                $walletLog->remark = "";
                $walletLog->save();
                // wallet_logs.balance更新到wallets
                $walletRenew = Wallet::where("user_id", "=", $user)->where("status", "=", 1)->first();
                $walletRenew->balance = WalletLog::where("user_id", "=", $user)->where("status", "=", 1)->orderBy('id', 'desc')->first()->balance;
                $walletRenew->save();

                return response()->json(['received' => 1]);
            }
        }
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
    // check_mac_value驗證器
    // input [] output string
    public function checkMacValue($rows)
    {

        $row = [];
        foreach ($rows as $key => $value) {
            if($key!= "check_mac_value"){
                $row[] = $key . '=' . $value;
            }
        }
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
        return $check_mac_value;
    }
}
