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

            if (sizeof($data) > 2) {

                $started = Carbon::parse($data[0]['created_at'])->unix();
                $ended = Carbon::parse($data[sizeof($data) -1]['created_at'])->unix();

                $results[] = [
                    'pair' => "$pair->s1$pair->s2",
                    'total_input' => $this->totalInput($data),
                    'value' => $this->value($data),
                    'wbw' => $this->wbw($data),
                    'seconds' => $ended - $started,
                    'started' => $started,
                    'ended' => $ended,
                    'diff_if_holding' => $this->value($data) - $this->wbw($data),
                    'profit' => $this->value($data) - $this->totalInput($data),
                ];
            }
        }

        $collection = collect($results);

        $started = $collection->min('started');
        $ended = $collection->max('ended');

        return [
            'data' => $results,
            'total_time' => $ended - $started,
        ];
    }

    public function wbw($data): int
    {
        return $data[sizeof($data) -1]['wbw_usd_1'] + $data[sizeof($data) -1]['wbw_usd_2'];
    }

    public function value($data): int
    {
        return $data[sizeof($data) -1]['balance_s1_usd'] + $data[sizeof($data) -1]['balance_s2_usd'];
    }

    public function totalInput($data): int
    {
        return $data[sizeof($data) -1]['input_symbol1_usd'] + $data[sizeof($data) -1]['input_symbol2_usd'];
    }
}
