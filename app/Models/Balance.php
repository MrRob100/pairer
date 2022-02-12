<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balance extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol',
        'balance',
        'balance_usd',
        'price_at_trade',
        'note',
        'side'
    ];
}
