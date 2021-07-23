<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class HitBTCService
{
    protected string $url = 'https://api.hitbtc.com/api/2/public/candles';

    public function getData($symbol): array
    {
        if (Cache::has('hitbtc')) {
            $data = Cache::get('hitbtc');
        } else {
            $data = json_decode(file_get_contents("{$this->url}?symbols={$symbol}USD&period=D1&limit=300"), true);
            Cache::put('hitbtc', $data, 8640);
        }

        return $data;
    }
}
