<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class BinanceGetService {

    public function apiCall($symbol, $interval = "1d")
    {
        if (Cache::has($symbol.$interval)) {
            $response = Cache::get($symbol.$interval);
        } else {
            $response = array_reverse(json_decode(file_get_contents("https://www.binance.com/api/v3/klines?symbol={$symbol}USDT&interval={$interval}"), true));

            Cache::put($symbol.$interval, $response, 600);
        }

        return $response;
    }

    public function price($symbol)
    {
        $data = json_decode(file_get_contents("https://www.binance.com/api/v3/ticker/price?symbol={$symbol}USDT"), true);

        return $data['price'];
    }

    public function getIcon($symbol): ?string
    {
        //gxs doesnt work

        $coinData = json_decode(file_get_contents(public_path('cmc.json')), true);

        $first = collect($coinData['data'])->where('symbol', '=', $symbol)->first();

        return $first ? "https://s2.coinmarketcap.com/static/img/coins/64x64/{$first['id']}.png" : false;
    }
}
