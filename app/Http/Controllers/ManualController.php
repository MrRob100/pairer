<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Services\BinanceGetService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\AccountService;
use Illuminate\Http\Request;

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

            $user = Auth::user();

            Log::info('transfer success data to log in db: '.json_encode($transfer). 'user id: ' . $user->id);
            //transfer is the order array

            $bal_all = $this->accountService->balance();

            $balance = $bal_all[$_GET['to']]['available'];
            $price = $transfer['fills'][0]['price'];

            //to after
            $b_record = Balance::create([
                'symbol' => $_GET['to'],
                'balance' => $balance,
                'balance_usd' => $balance * $price,
                'price_at_trade' => $price,
                'note' => 'after trade',
            ]);


            $b_record->user()->associate($user)->save();

            return true;

        } else {
            Log::error("transfer from {$_GET['from']} to {$_GET['to']} failed");
            return false;
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

    public function brecord(Request $request)
    {
        return $request->user()->balances()
            ->where('user_id', $request->user()->id)
            ->where('symbol', $request->c)
            ->limit(20)
            ->orderBy('created_at', 'DESC')
            ->get()->toArray();
    }
}
