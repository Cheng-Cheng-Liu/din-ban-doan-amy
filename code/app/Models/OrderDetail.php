<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'meal_id',
        'meal_another_id',
        'price',
        'quantity',
        'amount',
        'remark',
        'created_at',
    ];
}
