<?php

namespace App\Services;

use App\Models\Pair;
use App\Models\PairBalance;
use App\Services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccountService
{
    public $binanceGetService;

    public function __construct(BinanceGetService $binanceGetService)
    {
        $this->binanceGetService = $binanceGetService;
    }

    protected function api()
    {
        $data = Auth()->user()->key;

        return new Services\BinanceService($data->k, $data->s);
    }

    public function scale()
    {
        //get scale from db

        return 2.24;
    }

    public function balance(): array
    {
        $api = $this->api();
        $api->useServerTime();
        $ticker = $api->prices();

        return $api->balances($ticker);
    }

    public function sideToSide($from, $to, $portion)
    {
        //quantity measured in $from ($balance)

        $bals = $this->balance();

        $available = $bals[$from]['available'];

        $toPass = [
            'from' => $from,
            'to' => $to,
        ];

        $pair = Pair::where(
            function ($query) use ($toPass) {
                $query->where('s1', $toPass['from'])
                    ->where('s2', $toPass['to']);
            }
        )->orWhere(
            function ($query) use ($toPass) {
                $query->where('s1', $toPass['to'])
                    ->where('s2', $toPass['from']);
            }
        )->orderBy('created_at')->first();

//        if($pair->isNotEmpty()) {
            $price_s1 = $this->binanceGetService->price($pair->s1);
            $price_s2 = $this->binanceGetService->price($pair->s2);

            PairBalance::create([
                's1' => $pair->s1,
                'balance_s1' => $bals[$pair->s1]['available'],
                'balance_s1_usd' => $bals[$pair->s1]['available'] * $price_s1,
                'price_at_trade_s1' => $price_s1,
                's2' => $pair->s2,
                'balance_s2' => $bals[$pair->s2]['available'],
                'balance_s2_usd' => $bals[$pair->s2]['available'] * $price_s2,
                'price_at_trade_s2' => $price_s2,
            ]);
//        }

        $user = Auth::user();

        $to_init = $bals[$to]['available'];

        Log::info("(transfering from $from to $to ). Bals before transfer: $from: $available, $to: $to_init . user id: {$user->id}");
        //also db

        $bridgeBalBefore = $bals['USDT']['available'];
        $quantityTB = $available / $portion;

        Log::info("quant: $quantityTB of $from user: $user" ); //works

        $toBridge = $this->marketToUSDT($from, $quantityTB);

        if (!$toBridge) {
            Log::error('to bridge failed');
            return null;
        }

        //leaving time for order to be filled
        sleep(4);

        if ($toBridge['status'] !== 'FILLED') {
            Log::info('From bridge order not filled');
            return null;
        }

        $bals_after = $this->balance();

        $bal_from_after = $bals_after[$from]['available'];

        $bridgeBalAfter = $bals_after['USDT']['available'];

        if ($bridgeBalAfter > $bridgeBalBefore) {
            $quantityFB = $toBridge['executedQty']; //in from

            $quantityFBUSD = $quantityFB * $toBridge['fills'][0]['price']; //in USD

            $fromBridge = $this->marketFromUSDT($to, $quantityFBUSD);

            if (!$fromBridge) {
                $fromBridge = $this->marketFromUSDT($to, $quantityFBUSD, true);
                if (!$fromBridge) {
                    return null;
                }
            }

            if ($fromBridge['status'] === 'FILLED') {
                return $fromBridge;
            } else {
                Log::info('From bridge order not filled');
                return null;
            }

        } else {
            Log::info('Bridge bal after not bigger than bridge bal before');
            return null;
        }
    }

    /**
     * @param $to
     * @param $quantity
     * in usd
     * @return array|null
     */
    public function marketFromUSDT($to, $quantity, $roundMore = false)
    {
        //ROUND QUANTITY

        $api = $this->api();
        $api->useServerTime();

        $priceOfTo = $this->binanceGetService->price($to);

        $quantOfTo = $quantity / $priceOfTo;

        if ($quantOfTo > 100) {
            $quantityRounded = strval(floor($quantOfTo));
        } else {
            $quantityRounded = strval(floor($quantOfTo * 100)/100);
        }

        if ($roundMore) {
            $quantityRounded = strval(round($quantOfTo, 1));
            Log::info("2nd attempt USD to $to this time $quantityRounded");
        }

        Log::info("quant: $quantityRounded of $to"); //showing 0

        try {
            $order = $api->marketBuy("{$to}USDT", $quantityRounded);
        } catch (\Exception $e) {
            Log::error('error from USD to' . $to . ' ' . $e->getMessage());
            return null;
        }

        Log::info(json_encode($order));
        return $order;
    }

    public function marketToUSDT($from, $quantity)
    {
        //ROUND QUANTITY

        $api = $this->api();
        $api->useServerTime();

        //quantity of $fro
        try {
            $order = $api->marketSell("{$from}USDT", $quantity);
        } catch (\Exception $e) {
            Log::error('error to USD from' . $from . ' ' . $e->getMessage());
            return null;
        }

        Log::info(json_encode($order));

        return $order;
    }

    public function marketToBTC($from, $quantity)
    {

    }

    public function marketFromBTC($to)
    {

    }
}
