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
     * shows outstanding orders
     */
    public function orders()
    {
        $kucoin = App::make('App\Services\OrderService');

        /* orders */
        $orders = $kucoin->getOrders();

        return $orders;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
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
        $trigger->price = $request->get('price');

        // if immediately then go straigt to action
        if ($request->get('now')) {
            $order = App::make('App\Services\OrderService');
            $order->action($trigger);
        } else {
            //saving if not later
            $trigger->save();
        }

        return true;
    }

    public function delete($id)
    {
        $trigger_service = App::make('App\Services\TriggerService');

        $trigger_service->delete_trigger($id);

        return 0;
    }
}
