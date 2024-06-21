<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\CreditPayRecord;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Services\CheckMacValue;
use App\Http\Requests\PaymentRequest;
use Carbon\Carbon;
use Exception;

class PaymentController extends Controller
{
    public $amount;

    function recharge(PaymentRequest $request)
    {
        // 訂單編號
        $date = Carbon::now();
        $now = $date->format('YmdHis');
        $trade_no = 'mypay' . (string)$now;
        // 生成檢查碼
        $amount = $request->input('amount');
        $choose_payment = 'Credit';
        $encrypt_type = 1;
        $item_name = 'product 1';
        $lang = 'en';
        $merchant_id = 1;
        $merchant_trade_date = $date->format('Y/m/d H:i:s');
        $merchant_trade_no = $trade_no;
        $payment_type = 'aio';
        $return_url = 'http://192.168.83.28:9999/api/wallet/recharge/result';
        $trade_desc = '購買商品 1';
        $row = [
            'amount' => $amount,
            'choose_payment' => $choose_payment,
            'encrypt_type' => $encrypt_type,
            'item_name' => $item_name,
            'lang' => $lang,
            'merchant_id' => $merchant_id,
            'merchant_trade_date' => $merchant_trade_date,
            'merchant_trade_no' => $merchant_trade_no,
            'payment_type' => $payment_type,
            'return_url' => $return_url,
            'trade_desc' => $trade_desc
        ];
        // 生成check_mac_value
        $checkMacValue = new CheckMacValue;
        $check_mac_value = $checkMacValue->index($row);

        // http client
        $data = [
            'amount' => $amount,
            'choose_payment' => $choose_payment,
            'encrypt_type' => $encrypt_type,
            'item_name' => $item_name,
            'lang' => $lang,
            'merchant_id' => $merchant_id,
            'merchant_trade_date' => $merchant_trade_date,
            'merchant_trade_no' => $merchant_trade_no,
            'payment_type' => $payment_type,
            'return_url' => $return_url,
            'trade_desc' => $trade_desc,
            'check_mac_value' => $check_mac_value
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
            $creditPayRecord->remark = '';
            $creditPayRecord->payment_date = $merchant_trade_date;
            $creditPayRecord->trade_date = $merchant_trade_date;
            if (!$creditPayRecord->save()) {
                return response()->json(['error' => 'databaseExecError']);
            }
        } else {
            Log::channel('credit')->info('server_output' . $server_output);
        }


        return response()->json(['error' => $response]);
    }


    function rechargeResult(Request $request)
    {
        Log::channel('credit')->info($request);
        $getCheckMacValue = $request->input('check_mac_value');
        // 自己計算一次check_mac_value
        $checkMacValue = new CheckMacValue;
        $myCheckMacValue = $checkMacValue->index($request->all());
        // check_mac_value一致
        if ($myCheckMacValue == $getCheckMacValue) {
            $creditPayRecord = CreditPayRecord::where('merchant_trade_no', '=', $request->input('merchant_trade_no'))->first();

            // credit_pay_record.status改成trade_status的值
            $creditPayRecord->status = $request->input('rtn_code');
            $creditPayRecord->save();

            if ($request->input('rtn_code')) {
                // 確認這筆成功的信用卡紀錄是否已寫入過wallet_logs
                $creditPayRecordId = $creditPayRecord->id;
                $userId = $creditPayRecord->user_id;
                $walletLogRepeat = WalletLog::where('user_id', '=', $userId)->where('credit_pay_record_id', '=', $creditPayRecordId)->first();
                if ($walletLogRepeat) {
                    Log::channel('credit')->info('repeat credit_pay_record_id' . $walletLogRepeat->credit_pay_record_id);
                    return response()->json(['received' => 1]);
                }

                // wallets錢包更新
                try {
                    $wallet = Wallet::where('user_id', '=', $userId)->where('status', '=', 1)->where('wallet_type', '=', 1)->first();
                    $wallet->balance = $wallet->balance + $request->input('amount');
                    $wallet->save();
                } catch (Exception $e) {
                    Log::channel('credit')->info('wallet_logs' . $e);
                }

                // 增加wallet_logs紀錄
                try {
                    $walletLog = new WalletLog;
                    $walletLog->credit_pay_record_id = $creditPayRecordId;
                    $walletLog->user_id = $userId;
                    $walletLog->wallet_id = $wallet->id;
                    $walletLog->order_id = null;
                    $walletLog->amount = $request->input('amount');
                    $lastWallet = WalletLog::where('user_id', '=', $userId)->where('status', '=', 1)->orderBy('id', 'desc')->first();
                    $lastWalletBalance = 0;
                    if ($lastWallet == null) {
                        $lastWalletBalance = 0;
                    } else {
                        $lastWalletBalance = $lastWallet->balance;
                    }
                    $walletLog->balance = (int)$lastWalletBalance + (int)$request->input('amount');
                    $walletLog->status = 1;
                    $walletLog->remark = '';
                    $walletLog->save();
                } catch (Exception $e) {
                    Log::channel('credit')->info('wallet_logs' . $e);
                }

                return response()->json(['received' => 1]);
            }
        }
    }
}
