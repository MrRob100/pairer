<?php

namespace App\Http\Controllers;

use App\Models\Pair;
use App\Services\PairDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    public $pairDataService;

    public function __construct(PairDataService $pairDataService)
    {
        $this->pairDataService = $pairDataService;
    }

    public function index(Request $request): array
    {
        $pairs = Pair::where('state', $request->type)->get();

        $results = [];
        foreach ($pairs as $pair) {
            $fakedRequest = (object) [
                's1' => $pair->s1,
                's2' => $pair->s2,
                'month' => null,
            ];

            $data = array_values($this->pairDataService->getPairData($fakedRequest));

            if (sizeof($data) > 0) {
                $results[] = [
                    'pair' => "$pair->s1$pair->s2",
                    'total_input' => $data[sizeof($data) -1]['input_symbol1_usd'] + $data[sizeof($data) -1]['input_symbol2_usd'],
                    'value' => $data[sizeof($data) -1]['balance_s1_usd'] + $data[sizeof($data) -1]['balance_s2_usd'],
                    'wbw' => $data[sizeof($data) -1]['wbw_usd_1'] + $data[sizeof($data) -1]['wbw_usd_2'],
                    'seconds' => Carbon::parse($data[sizeof($data) -1]['created_at'])->unix() - Carbon::parse($data[0]['created_at'])->unix(),
                ];
            }
        }

        return $results;
    }
}
