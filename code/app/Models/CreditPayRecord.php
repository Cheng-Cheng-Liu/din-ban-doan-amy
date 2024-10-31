<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditPayRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'payment_type',
        'merchant_id',
        'merchant_trade_no',
        'card_no',
        'amount',
        'trade_desc',
        'item_name',
        'check_mac_value',
        'status',
        'remark',
        'payment_date',
        'trade_date'
    ];
}
