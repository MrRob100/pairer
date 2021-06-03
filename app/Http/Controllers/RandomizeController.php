<?php

namespace App\Http\Controllers;

use App\Models\DudPair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use function PHPUnit\Framework\isNull;

class RandomizeController extends Controller
{
    public function randomPair()
    {
        if (Cache::has('all')) {
            $data = Cache::get('all');
        } else {
            $data = collect(json_decode(file_get_contents('https://api3.binance.com/api/v3/ticker/24hr')));

            Cache::put('all', $data, 600);
        }

        $trash = DudPair::pluck('symbol')->all();

        $filtered = $data->filter(function ($value, $key) {
            return strpos($value->symbol, 'USDT') !== false ? $value : null;
        });

        $symbols = $filtered->pluck('symbol');

        $s1 = str_replace('USDT', '', $symbols[rand(0, $symbols->count())]);
        $s2 = str_replace('USDT', '', $symbols[rand(0, $symbols->count())]);

//        dump($s1.$s2);
//
//        dump(str_contains($s2.$s1, 'DOWN'));
//        dump(str_contains($s2.$s1, 'UP'));
//        dump(str_contains($s2.$s1, 'BEAR'));
//        dump(str_contains($s2.$s1, 'BULL'));
//
//        die();

        if(in_array($s1.$s2, $trash)
            || in_array($s2.$s1, $trash)
            || str_contains($s2.$s1, 'DOWN')
            || str_contains($s2.$s1, 'UP')
            || str_contains($s2.$s1, 'BEAR')
            || str_contains($s2.$s1, 'BULL'))
        {
           DudPair::create(['symbol' => $s1.$s2]);
           $this->randomPair();
        }

        return [
            'v1' => $s2,
            'v2' => $s1,
        ];
    }

    public function trash(Request $request)
    {
        if (!is_null($request->params['s1'] && !is_null($request->params['s2']))) {
            DudPair::create(['symbol' => $request->params['s1'].''.$request->params['s2']]);
        }
    }
}
