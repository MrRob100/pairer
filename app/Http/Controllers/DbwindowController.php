<?php

namespace App\Http\Controllers;

use App\Models\OpenOrder;
use App\Models\PairBalance;
use Illuminate\Http\Request;

class DbwindowController extends Controller
{
    public function index() {
        return view('dbwindow')->with([
            'pairBalances' => PairBalance::paginate(50),
            'openOrders' => OpenOrder::all(),
        ]);
    }
}
