<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'credit_pay_record_id',
        'user_id',
        'wallet_id',
        'order_id',
        'amount',
        'balance',
        'status',
        'remark',
    ];
}
