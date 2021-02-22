<?php

namespace App\Http\Controllers;

use App\Models\Pair;
use Illuminate\Http\Request;

class PairsController extends Controller
{
    public function index()
    {
        return Pair::all();
    }

    public function create(Request $request)
    {
        Pair::create($request->all()['params']);
    }

    public function delete(Request $request)
    {
        Pair::find($request->params['id'])->delete();
    }
}
