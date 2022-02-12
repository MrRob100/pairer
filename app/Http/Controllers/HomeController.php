<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Jenssegers\Agent\Agent;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function index(): View
    {
        $agent = new Agent();

        return view('home')->with('mobile', $agent->isMobile());
    }

    public function results(): View
    {
        return view('results');
    }
}
