<?php

namespace App\Http\Controllers;

use App\Services\BinanceGetService;
use App\Services\IEXGetService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public $binanceGetService;
    public $iexGetService;

    public function __construct(BinanceGetService $binanceGetService, IEXGetService $iexGetService)
    {
        $this->binanceGetService = $binanceGetService;
        $this->iexGetService = $iexGetService;
    }

    public function data(Request $request): array
    {
        if ($request->t === 'cryptos') {
            return $this->cryptos($request);
        }

        if ($request->t === 'oil') {
            return $this->oil($request);
        }

        return [];
    }

    public function cryptos(Request $request)
    {
        $response1 = $this->binanceGetService->apiCall($request->s1);
        $response2 = $this->binanceGetService->apiCall($request->s2);

        $size_max = max(sizeof($response1), sizeof($response2) - 1);
        $size_min = min(sizeof($response1), sizeof($response2) - 1);

        $pair = [];
        for($i=0; $i<$size_max; $i++) {

            if ($i < $size_min) {

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

        return [
            'first' => $this->formatBinanceResponse($response1),
            'pair' => array_reverse($pair),
            'second' => $this->formatBinanceResponse($response2),
        ];
    }

    public function oil(Request $request): array
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


        $formatted = [];
        for($i=0; $i<$size_max; $i++) {

            if ($i < $size_min) {
                $formatted[] = [
                    $data1[$i][0], //timestamp
                    $data1[$i][1] / $data2[$i][1],
                    $data1[$i][2] / $data2[$i][2],
                    $data1[$i][3] / $data2[$i][3],
                    $data1[$i][4] / $data2[$i][4],
//                $response1[$i][5], // volume
                ];
            }
        }

        return array_reverse($formatted);
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
