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
use Exception;
use App\Services\CheckMacValue;
use App\Http\Requests\PaymentRequest;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public $amount;

    function recharge(PaymentRequest $request)
    {
        // 訂單編號
        $date = Carbon::now();
        $now = $date->format('YmdHis');
        $trade_no = "mypay" . (string)$now;
        // 生成檢查碼
        $amount = $request->input('amount');
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
            "trade_desc" => $trade_desc
        ];
        // 生成check_mac_value
        $checkMacValue = new CheckMacValue;
        $check_mac_value = $checkMacValue->index($row);

        // http client
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
        $server_output = Http::post(config('services.recharge_url'), $data);
        $response_json = $server_output->throw()->json();

        if (array_key_exists('transaction_url', $response_json)) {
            $response = 0;
        } else {
            $response = 3002;
        }


        // // 把資料寫入credit_pay_records
        if ($response == 0) {
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


        return response()->json(['error' => $response]);
    }


    function rechargeResult(Request $request)
    {
        Log::channel('credit')->info($request);
        $check_mac_value = $request->input('check_mac_value');
        // 自己計算一次check_mac_value
        $checkMacValue = new CheckMacValue;
        $my_check_mac_value = $checkMacValue->index($request->all());
        // check_mac_value一致
        if ($my_check_mac_value == $check_mac_value) {
            $user = CreditPayRecord::where("merchant_trade_no", '=', $request->input('merchant_trade_no'))->first()->user_id;
            // credit_pay_record.status改成trade_status的值
            $creditPayRecordRtn = CreditPayRecord::where("merchant_trade_no", '=', $request->input('merchant_trade_no'))->first();
            $creditPayRecordRtn->status = $request->input('rtn_code');
            $creditPayRecordRtn->save();

            if ($request->input('rtn_code')) {
                // 該會員有沒有錢包
                $wallet = Wallet::where("user_id", "=", $user)->where("status", "=", 1)->where("wallet_type", "=", 1)->first();
                if ($wallet == null) {
                    // 新增一個主錢包
                    $addWallet = new Wallet;
                    $addWallet->user_id = $user;
                    $addWallet->balance = 0;
                    $addWallet->status = 1;
                    $addWallet->wallet_type = 1;
                    $addWallet->remark = "";
                    $addWallet->save();
                }

                // 確認這筆成功的信用卡紀錄是否已寫入過wallet_logs
                $credit_pay_record_id = CreditPayRecord::where("merchant_trade_no", '=', $request->input('merchant_trade_no'))->first()->id;

                $walletLogRepeat = WalletLog::where("user_id", "=", $user)->where("credit_pay_record_id", "=", $credit_pay_record_id)->first();

                if ($walletLogRepeat) {
                    Log::channel('credit')->info("repeat credit_pay_record_id" . $walletLogRepeat->credit_pay_record_id);

                    return response()->json(['received' => 1]);
                }
                // 增加wallet_logs紀錄
                try {
                    $walletLog = new WalletLog;
                    $walletLog->credit_pay_record_id = CreditPayRecord::where("merchant_trade_no", '=', $request->input('merchant_trade_no'))->first()->id;
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
                    $walletLog->balance = (int)$wallet_last_balance_balance + (int)$request->input('amount');
                    $walletLog->status = 1;
                    $walletLog->remark = "";
                    $walletLog->save();
                } catch (Exception $e) {
                    Log::channel('credit')->info("wallet_logs" . $e);
                }
                // wallet_logs.balance更新到wallets
                $walletBalanceOld = Wallet::where("user_id", "=", $user)->where("status", "=", 1)->where("wallet_type", "=", 1)->first()->balance;
                try {
                    $walletRenew = Wallet::where("user_id", "=", $user)->where("status", "=", 1)->where("wallet_type", "=", 1)->first();
                    $walletRenew->balance = $walletBalanceOld + $request->input('amount');
                    $walletRenew->save();
                } catch (Exception $e) {
                    Log::channel('credit')->info("wallet_logs" . $e);
                }
                return response()->json(['received' => 1]);
            }
        }
    }
}
