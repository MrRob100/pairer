<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Services\BinanceGetService;
use Illuminate\Support\Facades\Log;
use App\Services\AccountService;

class ManualController extends Controller
{
    public $accountService;
    public $binanceGetService;

    public function __construct(AccountService $accountService, BinanceGetService $binanceGetService)
    {
        $this->accountService = $accountService;
        $this->binanceGetService = $binanceGetService;
    }

    public function transfer()
    {
        $transfer = $this->accountService->sideToSide($_GET['from'], $_GET['to'], $_GET['portion']);

        if ($transfer) {
            Log::info('transfer success data to log in db: '.$transfer);
            //transfer is the order array

//            Balance::create([
//                'name' => 'London to Paris',
//            ]);

            //FIND PAIR TRYING BOTH COMBINATIONS OR PASS IN FROM VUE AS A GET VARIABLE
//            Balance::insert(['what' => 'ever']);
        } else {
            Log::error("transfer from {$_GET['from']} to {$_GET['to']} failed");
        }
    }

    public function balance()
    {
        return $this->accountService->balance()[$_GET['of']]['available'];
    }

    public function price()
    {
        return $this->binanceGetService->price($_GET['symbol']);
    }
}
