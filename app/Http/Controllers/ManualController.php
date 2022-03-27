<?php

namespace App\Http\Controllers;

use App\Models\Input;
use App\Models\PairBalance;
use App\Services\BinanceGetService;
use App\Services\CmcService;
use App\Services\PairDataService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\AccountService;
use Illuminate\Http\Request;

class ManualController extends Controller
{
    public $accountService;
    public $binanceGetService;
    public $cmcService;
    public $pairDataService;

    public function __construct(
        AccountService $accountService,
        BinanceGetService $binanceGetService,
        CmcService $cmcService,
        PairDataService $pairDataService,
    )
    {
        $this->accountService = $accountService;
        $this->binanceGetService = $binanceGetService;
        $this->cmcService = $cmcService;
        $this->pairDataService = $pairDataService;
    }

    public function shave()
    {

    }

    public function pump()
    {

    }

    public function transfer()
    {
        $transfer = $this->accountService->sideToSide($_GET['from'], $_GET['to'], $_GET['portion']);

        if ($transfer) {

            $user = Auth::user();

            Log::info('transfer success data to log in db: '.json_encode($transfer));

            return true;

        } else {
            Log::error("transfer from {$_GET['from']} to {$_GET['to']} failed");
            return false;
        }
    }

    public function balance()
    {
        $balance = $this->accountService->balance()[$_GET['of']];

        return floatval($balance['available']) + floatval($balance['onOrder']);
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
        return [
            'records' => $this->pairDataService->getPairData($request),
            'current_month' => $request->month ?: Carbon::now()->month,
            'months' => [
                0 => [
                    'value' => 1,
                    'name' => 'Jan',
                ],
                [
                    'value' => 2,
                    'name' => 'Feb',
                ],
                [
                    'value' => 3,
                    'name' => 'Mar',
                ],
                [
                    'value' => 4,
                    'name' => 'Apr',
                ],
                [
                    'value' => 5,
                    'name' => 'May',
                ],
                [
                    'value' => 6,
                    'name' => 'Jun',
                ],
                [
                    'value' => 7,
                    'name' => 'Jul',
                ],
                [
                    'value' => 8,
                    'name' => 'Aug',
                ],
                [
                    'value' => 9,
                    'name' => 'Sep',
                ],
                [
                    'value' => 10,
                    'name' => 'Oct',
                ],
                [
                    'value' => 11,
                    'name' => 'Nov',
                ],
                [
                    'value' => 12,
                    'name' => 'Dec',
                ],
            ]
        ];
    }

    public function icon(Request $request)
    {
        return $this->binanceGetService->getIcon($request->symbol);
    }

    public function getLimits(Request $request): array
    {
        return $this->accountService->getOpenOrders(strtoupper($request->symbol1.$request->symbol2));
    }

    public function limitBuy(Request $request): array
    {
        return $this->accountService->limitBuy($request->symbol1.$request->symbol2, '');
    }
}
