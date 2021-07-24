<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CmcService
{
    protected string $url = 'https://api.coinmarketcap.com/data-api/v3/cryptocurrency/detail/chart?id=2444&range=3M';

    public function getData(): array
    {
        if (Cache::has('cmc')) {
            $data = Cache::get('cmc');
        } else {
            $data = json_decode(file_get_contents($this->url), true);
            Cache::put('cmc', $data, 8640);
        }

        return $data;
    }
}





























