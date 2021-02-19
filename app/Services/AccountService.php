<?php

namespace App\Services;

use App\Models\Balance;
use App\Services;
use Binance\API;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\BinanceGetService;

class AccountService
{
    public $binanceGetService;

    public function __construct(BinanceGetService $binanceGetService)
    {
        $this->binanceGetService = $binanceGetService;
    }

    protected function api()
    {
        $data = Auth()->user()->key()->first();

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

        //log bal of from
        $b_record = Balance::create([
            'symbol' => $from,
            'balance' => $available,
            'balance_usd' => $available * $this->binanceGetService->price($from),
        ]);
        $b_record->user()->associate(Auth::user())->save();

        $to_init = $bals[$to]['available'];

        Log::info("(transfering from $from to $to ). Bals before transfer: $from: $available, $to: $to_init");
        //also db

        $bridgeBalBefore = $bals['USDT']['available'];
        $quantityTB = $available / $portion;

        Log::info("quant: $quantityTB of $from"); //works

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

        $bridgeBalAfter = $bals_after['USDT']['available'];

        if ($bridgeBalAfter > $bridgeBalBefore) {
            $quantityFB = $toBridge['executedQty']; //in from

            $quantityFBUSD = $quantityFB * $toBridge['fills'][0]['price']; //in USD

            $fromBridge = $this->marketFromUSDT($to, $quantityFBUSD);

            if (!$fromBridge) {
                return null;
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
    public function marketFromUSDT($to, $quantity)
    {
        //ROUND QUANTITY

        $api = $this->api();
        $api->useServerTime();

        $priceOfTo = $this->binanceGetService->price($to);

        $quantOfTo = $quantity / $priceOfTo;

        //quantity of $to

        $quantityRounded = strval(floor($quantOfTo * 100)/100);

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
