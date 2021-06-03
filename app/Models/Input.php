<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Input extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol1',
        'amount1',
        'amount1_usd',
        'symbol2',
        'amount2',
        'amount2_usd',
    ];

    public function pair(): BelongsTo
    {
        return $this->belongsTo(Pair::class);
    }
}
