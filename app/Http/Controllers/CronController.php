<?php

namespace App\Http\Controllers;

use App\Models\Target;

class CronController extends Controller
{
    public function check()
    {
        $price = floatval(json_decode(
            file_get_contents('https://www.binance.com/api/v3/ticker/price?symbol=XMRBNB'), true
        )['price']);

        $upper = floatval(Target::first()->upper);
        $lower = floatval(Target::first()->lower);

        if ($price >= $upper) {
            Log::info("get XMR. Price is $price and range is $upper and $lower");
        } elseif ($price <= $lower) {
            Log::info("get BNB. Price is $price and range is $upper and $lower");
        } else {
            Log::info("checked and price within range. Price: $price, upper: $upper, lower: $lower");
        }s
    }
}
