<?php

namespace App\Models;

use App\Helpers\PairHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pair extends Model
{
    use HasFactory;

    protected $fillable = [
        's1',
        's2',
        'state',
    ];

    public function scopeWherePure($query) {
        return $query->whereIn('s1', PairHelper::PURES)->orWhereIn('s2', PairHelper::PURES);
    }
}
