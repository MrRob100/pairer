<?php

namespace App\Http\Controllers;

use App\Models\OpenOrder;
use App\Models\PairBalance;

class DbwindowController extends Controller
{
    public function index() {
        return view('dbwindow')->with([
            'pairBalances' => PairBalance::where('s1', 'RIF')->where('s3', 'BTC')->all(),
            'openOrders' => OpenOrder::all(),
        ]);
    }
}
