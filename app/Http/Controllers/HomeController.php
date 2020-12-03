<?php

namespace App\Http\Controllers;

use App\Models\Target;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home')
            ->with('target', Target::first());
    }

    public function cancel($order_id)
    {
        return $response;
    }

    public function balance($coin)
    {
        return $balance;
    }

    public function price($coin)
    {
        return $price;
    }
}
