<?php

namespace App\Services;

use App\Helpers\PairHelper;
use App\Models\OpenOrder;
use App\Models\Pair;
use App\Models\PairBalance;
use App\Services;
use Illuminate\Support\Facades\Cache;
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
        $key = env('BINANCE_KEY');
        $secret = env('BINANCE_SECRET');

        return new Services\BinanceService($key, $secret);
    }

    public function scale()
    {
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

        $to_init = $bals[$to]['available'];

        Log::info("(transfering from $from to $to ). Bals before transfer: $from: $available, $to: $to_init .");
        //also db

        $bridgeBalBefore = $bals['USDT']['available'];
        $quantityTB = $available / $portion;

        Log::info("quant: $quantityTB of $from " ); //works

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

    public function getOpenOrders(string $symbol1, string $symbol2): array
    {
        $this->checkForFills([['symbol1' => $symbol1, 'symbol2' => $symbol2]]);
        $api = $this->api();
        $api->useServerTime();

        $balance = $this->balance();

        $total1 = $balance[$symbol1]['onOrder'] + $balance[$symbol1]['available'];
        $total2 = $balance[$symbol2]['onOrder'] + $balance[$symbol2]['available'];

        $pureClass = app()->make(PairHelper::class);

        if (!$pureClass->isPure($symbol1, $symbol2)) return [
            'success' => false,
            'error' => 'impure pair'
        ];

        $symbols = [
            1 => $symbol1,
            2 => $symbol2,
        ];

        $plb = OpenOrder::whereHas(
            'pairBalance',
            function($query) use ($symbols) {
                $query->where(['s1' => $symbols[1], 's2' => $symbols[2]]);
            }
        )->where(['status' => 'NEW', 'side' => 'buy'])->first()?->pure_price_at_trade;

        $psls = OpenOrder::whereHas(
            'pairBalance',
            function($query) use ($symbols) {
                $query->where(['s1' => $symbols[1], 's2' => $symbols[2]]);
            }
        )->where(['status' => 'NEW', 'side' => 'sell'])->first()?->pure_price_at_trade;

        try {
            return [
                'order_balance_percentage' => [
                    'symbol1' => $total1 == 0 ?: ($balance[$symbol1]['onOrder'] / $total1) * 100,
                    'symbol2' => $total2 == 0 ?: ($balance[$symbol2]['onOrder'] / $total2) * 100,
                ],
                'plb' => $plb,
                'psls' => $psls,
                'success' => true,
            ];
        } catch(\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function cancelOrders($symbol1, $symbol2, $side)
    {
        $api = $this->api();
        $api->useServerTime();

        $orders = $this->getOpenOrders($symbol1, $symbol2);

        foreach ($orders[0] as $order) {
            if ($order['side'] === $side) {
                $api->cancel($symbol1.$symbol2, $order['orderId']);

                OpenOrder::where('orderId', $order['orderId'])->pairBalance->delete();
            }
        }
    }

    public function limitBuy($symbol1, $symbol2, $price, $portion, $lotSize): array
    {
        $this->cancelOrders($symbol1, $symbol2, 'BUY');

        //eg btc to rif, want price (of rif) to be low
        $api = $this->api();
        $api->useServerTime();

        $pair = $symbol1.$symbol2;

        $bals = $this->balance();

        $s1_available = $bals[$symbol1]['available'];
        $s2_available = $bals[$symbol2]['available'];

        $quantity = number_format(($s2_available * ($portion / 100)) / $price, 7);

        $quantityChopped = floor(str_replace(',', '', $quantity) / $lotSize) * $lotSize;

        $pairBalance = PairBalance::create([
            's1' => $symbol1,
            'balance_s1' => $s1_available + $bals[$symbol1]['onOrder'],
            's2' => $symbol2,
            'balance_s2' => $s2_available + $bals[$symbol2]['onOrder'],
        ]);

        $order = $api->order('BUY', $pair, $quantityChopped, $price, 'LIMIT');

        $pairBalance->openOrders()->create([
            'orderId' => $order['orderId'],
            'status' => $order['status'],
            'pure_price_at_trade' => $price,
            'side' => 'sell',
        ]);

        return $order;
    }

    public function stopLimitSell($symbol1, $symbol2, $price, $portion, $lotSize): array
    {
        $this->cancelOrders($symbol1, $symbol2, 'SELL');

        $api = $this->api();
        $api->useServerTime();

        $pair = $symbol1.$symbol2;

        $bals = $this->balance();

        $s1_available = $bals[$symbol1]['available'];
        $s2_available = $bals[$symbol2]['available'];

        $quantity = number_format(($s2_available * ($portion / 100) / $price), 7);

        $quantityChopped = floor(str_replace(',', '', $quantity) / $lotSize) * $lotSize;

        $pairBalance = PairBalance::create([
            's1' => $symbol1,
            'balance_s1' => $s1_available + $bals[$symbol1]['onOrder'],
            's2' => $symbol2,
            'balance_s2' => $s2_available + $bals[$symbol2]['onOrder'],
        ]);

        $order = $api->order('SELL', $pair, $quantityChopped, $price, 'LIMIT');

        $pairBalance->openOrders()->create([
            'orderId' => $order['orderId'],
            'status' => $order['status'],
            'pure_price_at_trade' => $price,
            'side' => 'sell',
        ]);

        return $order;
    }

    public function getLotSize($pair): float
    {
        $api = $this->api();
        $filters = collect($api->exchangeInfo()['symbols'][$pair]['filters']);

        return floatval($filters->where('filterType', 'LOT_SIZE')->first()['stepSize']);
    }

    public function checkForFills(array $pairs)
    {
        $api = $this->api();
        $api->useServerTime();

        foreach($pairs as $pair) {
            $symbol1 = strtoupper($pair['symbol1']);
            $symbol2 = strtoupper($pair['symbol2']);
            $symbols = [$symbol1, $symbol2];

            $openOrders = OpenOrder::whereHas(
                'pairBalance',
                function ($query) use ($symbols) {
                    $query->where('s1', $symbols[0])->where('s2', $symbols[1]);
                }
            )->orderBy('fill_time');

            foreach($openOrders as $openOrder) {
                $status = $api->orderStatus($symbol1.$symbol2, $openOrder->orderId);
//              "NEW", "FILLED", "CANCELLED"

                if ($status['status'] === 'FILLED') {
                    $price_at_trade_s1_usd = $this->getPriceAtTradeUSD($status['updateTime'], $symbol1);
                    $price_at_trade_s2_usd = $this->getPriceAtTradeUSD($status['updateTime'], $symbol2);

                    $openOrder->update(
                        [
                            'fill_time' => $status['updateTime'],
                            'status' => 'FILLED',
                        ]
                    );

                    $pairBalance = $openOrder->pairBalance;

                    $pairBalance->update(
                        [
                            'price_at_trade_s1' => $price_at_trade_s1_usd,
                            'balance_s1_usd' => $price_at_trade_s1_usd * $pairBalance->balance_s1,
                            'price_at_trade_s2' => $price_at_trade_s2_usd,
                            'balance_s2_usd' => $price_at_trade_s2_usd * $pairBalance->balance_s2,
                        ]
                    );
                }
            }
        }
    }

    public function getPriceAtTradeUSD($fillTime, string $symbol): ?string
    {
        $startTime = $fillTime - 30000;
        $fillTimeInt = intval(floor($fillTime / 1000) * 1000);
        $response = json_decode(file_get_contents("https://www.binance.com/api/v3/klines?symbol={$symbol}USDT&interval=1m&startTime={$startTime}"), true);
        $closest = null;
        $price = null;
        foreach ($response as $item) {
            if ($closest === null || abs($fillTimeInt - $closest) > abs($item[0] - $fillTimeInt)) {
                $closest = $item[0];
                $price = $item[1];
            }
        }

        return $price;
    }

}
