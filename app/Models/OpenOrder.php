<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'orderId',
        'fill_time',
        'status',
        'pure_price_at_trade',
        'side',
    ];

    public function pairBalance(): BelongsTo
    {
        return $this->belongsTo(PairBalance::class);
    }
}
