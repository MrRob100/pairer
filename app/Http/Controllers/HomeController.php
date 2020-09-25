<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Support\Facades\App;
use App\Models\OrderModel;
use App\Models\Trigger;

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
        $kucoin = App::make('App\Services\OrderService');

        /* orders */
        $orders = $kucoin->getOrders();
        // dump('orders \/');

        /* positions */
        $positions = $kucoin->getPositions();
        // dump('positions \/');
        // dump($positions);

        return view('home', compact('orders', 'positions'));
    }

    public function balance($coin)
    {
        $kucoin = App::make('App\Services\OrderService');

        $balance = $kucoin->getAvailableBalance(strtoupper($coin));

        return $balance;
    }

    public function price($coin)
    {
        $kucoin = App::make('App\Services\OrderService');

        $price = $kucoin->getPrice(strtoupper($coin));

        return $price;
    }

    public function triggers()
    {
        $trigger = new Trigger();
        $triggers = $trigger->all();
        return $triggers;
    }

    public function addTrigger(Request $request)
    {

        $trigger = new Trigger();
        $trigger->when = $request->get('when');
        $trigger->amount = $request->get('amount');
        $trigger->pair = $request->get('pair');
        $trigger->type = $request->get('type');
        $trigger->side = $request->get('side');
        $trigger->save();

        return true;
    }

    public function delete($id)
    {
        $trigger = new Trigger();
        $res = $trigger::where('id', $id)->delete();
    }
}
