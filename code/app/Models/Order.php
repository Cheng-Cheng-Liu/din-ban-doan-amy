<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'restaurant_id',
        'name',
        'another_id',
        'choose_payment',
        'amount',
        'status',
        'remark',
        'pick_up_time',
        'created_at',
    ];
}
