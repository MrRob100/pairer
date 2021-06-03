<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Services\BinanceGetService;
use App\Services\IEXGetService;
use App\Services\PandaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public $binanceGetService;
    public $iexGetService;
    public $pandaService;

    public function __construct(BinanceGetService $binanceGetService, IEXGetService $iexGetService, PandaService $pandaService)
    {
        $this->binanceGetService = $binanceGetService;
        $this->iexGetService = $iexGetService;
        $this->pandaService = $pandaService;
    }

    public function data(Request $request): array
    {
        if ($request->t === 'goldpaxg') {
            return $this->goldpaxg();
        }

        if ($request->t === 'binance') {
            return $this->binance($request);
        }

        if ($request->t === 'oil' || $request->t === 'iex') {
            return $this->iex($request);
        }

        return [];
    }

    public function goldpaxg()
    {
//        NEED TO FINISH

        $response1 = array_reverse($this->pandaService->getData('XAU'));
        $response2 = array_reverse($this->formatBinanceResponse($this->binanceGetService->apiCall('PAXG', '4h')));

        $size_max = max(sizeof($response1), sizeof($response2) - 1);
        $size_min = min(sizeof($response1), sizeof($response2) - 1);

        $pair = [];
        for($i=0; $i<$size_max; $i++) {

            if ($i < $size_min) {

                dump('t1'.$i.' '.gmdate("d-m-y", $response1[$i][0] / 1000)); //4 of those per day
                dump('t2'.$i.' '.gmdate("d-m-y", $response2[$i][0] / 1000)); //6 of those per day
                echo('------');

                $pair[] = [
                    $response1[$i][0], //timestamp
                    $response1[$i][1] / $response2[$i][1],
                    $response1[$i][2] / $response2[$i][2],
                    $response1[$i][3] / $response2[$i][3],
                    $response1[$i][4] / $response2[$i][4],
//                $response1[$i][5], // volume
                ];
            }
        }

        return [
            'first' => $response1,
            'pair' => $pair,
            'second' => $response2,
        ];

//        return [
//            'first' => array_reverse($response1),
//            'pair' => $pair,
//            'second' => array_reverse($response2),
//        ];

    }

    public function binance(Request $request)
    {
        if (file_exists(public_path() . "/" . $request->s1 . ".csv")) {
            unlink(public_path() . "/" . $request->s1 . ".csv");
        }

        if (file_exists(public_path() . "/" . $request->s2 . ".csv")) {
            unlink(public_path() . "/" . $request->s2 . ".csv");
        }

        $response1 = $this->binanceGetService->apiCall($request->s1);
        $response2 = $this->binanceGetService->apiCall($request->s2);

        $size_max = max(sizeof($response1), sizeof($response2) - 1);
        $size_min = min(sizeof($response1), sizeof($response2) - 1);

        $file1 = fopen(public_path() . "/" . $request->s1 . ".csv", "w");
        $file2 = fopen(public_path() . "/" . $request->s2 . ".csv", "w");

        $pair = [];
        for($i=0; $i<$size_max; $i++) {

            if ($i < $size_min) {

                fputcsv($file1, $response1[$i]);
                fputcsv($file2, $response2[$i]);

//                dump('t1'.$i.' '.gmdate("d-m-y", $response1[$i][0] / 1000));
//                dump('t2'.$i.' '.gmdate("d-m-y", $response2[$i][0] / 1000));
//                echo('------');

                $pair[] = [
                    $response1[$i][0], //timestamp
                    $response1[$i][1] / $response2[$i][1],
                    $response1[$i][2] / $response2[$i][2],
                    $response1[$i][3] / $response2[$i][3],
                    $response1[$i][4] / $response2[$i][4],
//                $response1[$i][5], // volume
                ];
            }
        }

        fclose($file1);
        fclose($file2);

        $top1 = Balance::where('symbol', $request->s1)
            ->where('side', 'sell')
            ->orderBy('created_at')->limit(1)->first() ?
            Balance::where('symbol', $request->s1)
            ->where('side', 'sell')
            ->orderBy('created_at', 'DESC')->limit(1)->first()->price_at_trade : null;

        $top2 = Balance::where('symbol', $request->s2)
            ->where('side', 'buy')
            ->orderBy('created_at')->limit(1)->first()?
            Balance::where('symbol', $request->s2)
            ->where('side', 'buy')
            ->orderBy('created_at', 'DESC')->limit(1)->first()->price_at_trade : null;

        $bot1 = Balance::where('symbol', $request->s1)
            ->where('side', 'buy')
            ->orderBy('created_at')->limit(1)->first() ? Balance::where('symbol', $request->s1)
            ->where('side', 'buy')
            ->orderBy('created_at', 'DESC')->limit(1)->first()->price_at_trade : null;

        $bot2 = Balance::where('symbol', $request->s2)
            ->where('side', 'sell')
            ->orderBy('created_at')->limit(1)->first() ? Balance::where('symbol', $request->s2)
            ->where('side', 'sell')
            ->orderBy('created_at', 'DESC')->limit(1)->first()->price_at_trade : null;

        return [
            'first' => $this->formatBinanceResponse($response1),
            'pair' => array_reverse($pair),
            'second' => $this->formatBinanceResponse($response2),
            'events' => [
                'top1' => $top1,
                'top2' => $top2,
                'bot1' => $bot1,
                'bot2' => $bot2,
            ]
        ];
    }

    public function iex(Request $request): array
    {
        $response1 = $this->iexGetService->apiCall($request->s1);
        $response2 = $this->iexGetService->apiCall($request->s2);

        $size_max = max(sizeof($response1['chart']), sizeof($response2['chart']) - 1);
        $size_min = min(sizeof($response1['chart']), sizeof($response2['chart']) - 1);

        $data1 = array_map(function($item) {
            return [
                strtotime($item['date']) * 1000,
                $item['open'],
                $item['high'],
                $item['low'],
                $item['close'],
            ];
        }, $response1['chart']);

        $data2 = array_map(function($item) {
            return [
                strtotime($item['date']) * 1000,
                $item['open'],
                $item['high'],
                $item['low'],
                $item['close'],
            ];
        }, $response2['chart']);

        $pair = [];
        for($i=0; $i<$size_max; $i++) {

            if ($i < $size_min) {
                $pair[] = [
                    $data1[$i][0], //timestamp
                    $data1[$i][1] / $data2[$i][1],
                    $data1[$i][2] / $data2[$i][2],
                    $data1[$i][3] / $data2[$i][3],
                    $data1[$i][4] / $data2[$i][4],
//                $response1[$i][5], // volume
                ];
            }
        }

        return [
            'first' => $this->formatIEXResponse($response1),
            'pair' => array_reverse($pair),
            'second' => $this->formatIEXResponse($response2),
        ];

//        return array_reverse($formatted);
    }

    public function metals()
    {

    }

    public function others()
    {

    }

    public function formatBinanceResponse(array $data): array
    {
        $formatted = [];
        foreach($data as $item) {
            $formatted[] = [
                floatval($item[0]),
                floatval($item[1]),
                floatval($item[2]),
                floatval($item[3]),
                floatval($item[4]),
            ];
        }

        return array_reverse($formatted);
    }

    public function formatIEXResponse(array $data): array
    {
        $formatted = array_map(function ($item) {
            return [
                strtotime($item['date']) * 1000,
                $item['open'],
                $item['high'],
                $item['low'],
                $item['close'],
            ];
        }, $data['chart']);

        return array_reverse($formatted);
    }

    public function pair(): View
    {
        return view('pair');
    }
}
