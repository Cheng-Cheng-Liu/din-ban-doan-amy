<?php

namespace App\Services;

class CheckMacValue {

    // check_mac_value驗證器
    // input [] output string
    public function index($rows)
    {
       
        $row = [];
        foreach ($rows as $key => $value) {
            if ($key != "check_mac_value") {
                $row[] =  $key . '=' . $value;
            }
        }
        sort($row);

        $rowString = "";
        foreach ($row as $one) {
            $add = $one . "&";
            $rowString .= $add;
        }

        $rowCheck = "hash_key=".config('services.hash_key')."&". $rowString . "hash_iv=".config('services.hash_iv');
        // urlencode
        $urlencodeRow = $this->ecpayUrlEncode($rowCheck);
        // hash256
        $hashRow = hash("sha256", $urlencodeRow);
        $check_mac_value = strtoupper($hashRow);
        return $check_mac_value;
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



