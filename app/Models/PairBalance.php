<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PairBalance extends Model
{
    protected $fillable = [
        's1',
        'balance_s1',
        'balance_s1_usd',
        'price_at_trade_s1',
        's2',
        'balance_s2',
        'balance_s2_usd',
        'price_at_trade_s2',
    ];

    use HasFactory;
}
