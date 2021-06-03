<?php

namespace App\Http\Controllers;

use App\Models\Target;
use Illuminate\Support\Facades\Log;
use App\Services\AccountService;

class CronController extends Controller
{
    public $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function check()
    {
        $price = floatval(json_decode(
            file_get_contents('https://www.binance.com/api/v3/ticker/price?symbol=XMRBNB'), true
        )['price']);

        $upper = floatval(Target::first()->upper);
        $lower = floatval(Target::first()->lower);

        if ($price >= $upper) {
            Log::info("get BNB. Price is $price and range is $upper and $lower");

            //params, from, to
            $transfer = $this->accountService->sideToSide('XMR', 'BNB', 2.24);

            if (!$transfer) {
                Log::info('transfer from XMR to BNB failed');
            }

            //do something with transfer resp obj

        } elseif ($price <= $lower) {
            Log::info("get XMR. Price is $price and range is $upper and $lower");

            //params, from, to
            $transfer = $this->accountService->sideToSide('BNB', 'XMR', 2.24);

            if (!$transfer) {
                Log::info('transfer from BNB to XMR failed');
            } else {
                Log::info('transfer from BNB to XMR successful');
            }

        } else {
            Log::info("checked and price within range. Price: $price, upper: $upper, lower: $lower");
        }
    }
}
