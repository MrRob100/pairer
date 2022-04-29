<?php

namespace App\Services;

use App\Models\Input;
use App\Models\PairBalance;
use Illuminate\Support\Carbon;

class PairDataService {
    public function getPairData($request): array
    {
        $month = $request->month;

        $year = $month > Carbon::now()->month ? Carbon::now()->subYear()->year : Carbon::now()->year;
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = Carbon::createFromDate($year, $month, 1)->addMonth();

        $query = PairBalance::where('s1', $request->s1)
            ->where('s2', $request->s2)
            ->whereNotNull('balance_s1_usd')
            ->whereNotNull('price_at_trade_s1')
            ->whereNotNull('balance_s2_usd')
            ->whereNotNull('price_at_trade_s2');

        if ($month) {
            $query = $query->whereBetween('updated_at', [$startDate, $endDate]);
        }

        $pair_balances = $query->orderBy('updated_at')->get();

        $inputsQuery = Input::where(
            function ($query) use ($request) {
                $query->where('symbol1', $request->s1)
                    ->where('symbol2', $request->s2);
            }
        )->orWhere(
            function ($query) use ($request) {
                $query->where('symbol1', $request->s2)
                    ->where('symbol2', $request->s1);
            }
        );

        if ($month) {
            $inputsQuery = $inputsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $inputs = $inputsQuery->orderBy('created_at')->get();

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
                        'wbw_usd_2' => $relInputs->sum('amount2') * $pair_balance->price_at_trade_s2,
                        'cix' => null
                    ]);

                    $data[] = $merged;
                }
            }
        }
        return  collect($data)->unique()->toArray();
    }
}
