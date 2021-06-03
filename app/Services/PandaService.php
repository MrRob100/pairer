<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PandaService
{
    protected string $url = 'https://api.bitpanda.com/v1/ohlc/usd/month';

    public function apiCall(): array
    {
        if (Cache::has('panda')) {
            $data = Cache::get('panda');
        } else {
            $data = json_decode(file_get_contents($this->url), true)['data'];
            Cache::put('panda', $data, 600);
        }
        return $data;
    }

    public function getData($symbol): array
    {
        $data = [];
        foreach($this->apiCall()[$symbol] as $item) {
            array_push($data, [
                $item['attributes']['time']['unix'] * 1000,
                $item['attributes']['open'],
                $item['attributes']['high'],
                $item['attributes']['low'],
                $item['attributes']['close'],
            ]);
        }

        return $data;
    }
}
