<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class BinanceGetService {

    public function apiCall($symbol)
    {
        if (Cache::has($symbol)) {
            $response = Cache::get($symbol);
        } else {
            $response = array_reverse(json_decode(file_get_contents("https://www.binance.com/api/v3/klines?symbol={$symbol}USDT&interval=1d"), true));

            Cache::put($symbol, $response, 600);
        }

        return $response;
    }

    public function price($symbol)
    {
        $data = json_decode(file_get_contents("https://www.binance.com/api/v3/ticker/price?symbol={$symbol}USDT"), true);

        return $data['price'];
    }
}
