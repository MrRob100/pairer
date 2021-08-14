<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInputsRequest;
use App\Models\Input;

class InputController extends Controller
{
    public function create(CreateInputsRequest $request) {

        Input::create([
            'symbol1' => $request->symbol1,
            'amount1' => $request->one,
            'amount1_usd' => $request->oneUSD,
            'symbol2' => $request->symbol2,
            'amount2' => $request->two,
            'amount2_usd' => $request->twoUSD,
        ]);

        return true;
    }
}
