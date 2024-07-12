<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'tag',
        'phone',
        'opening_time',
        'closing_time',
        'rest_day',
        'status',
        'avg_score',
        'total_comments_count',
        'priority',
    ];
}
