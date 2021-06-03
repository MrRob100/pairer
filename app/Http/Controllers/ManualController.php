<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Input;
use App\Models\PairBalance;
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
                'side' => 'buy',
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

    public function getPairData(Request $request)
    {
        $pair_balances = PairBalance::where('s1', $request->s1)
            ->where('s2', $request->s2)
            ->orderBy('created_at')->get();

        $inputs = Input::where(
            function ($query) use ($request) {
                $query->where('symbol1', $request->s1)
                    ->where('symbol2', $request->s2);
            }
        )->orWhere(
            function ($query) use ($request) {
                $query->where('symbol1', $request->s2)
                    ->where('symbol2', $request->s1);
            }
        )->orderBy('created_at')->get();

        $data = [];
        foreach($pair_balances as $pair_balance) {
            for($i = 0; $i < sizeof($inputs); $i++) {
                if ($inputs[$i]->created_at <= $pair_balance->created_at) {

                    $relInputs = $inputs->where('created_at', '<=', $pair_balance->created_at);

                    $merged = array_merge($pair_balance->toArray(), [
                        'input_symbol1' => $relInputs->sum('amount1'),
                        'input_symbol1_usd' => $relInputs->sum('amount1_usd'),
                        'input_symbol2' => $relInputs->sum('amount2'),
                        'input_symbol2_usd' => $relInputs->sum('amount2_usd'),
                        'wbw_usd_1' => $relInputs->sum('amount1') * $pair_balance->price_at_trade_s1,
                        'wbw_usd_2' => $relInputs->sum('amount2') * $pair_balance->price_at_trade_s2
                    ]);

                    $data[] = $merged;
                }
            }
        }
        return $data;
    }
}
