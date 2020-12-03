<?php

namespace App\Http\Controllers;

use App\Models\Target;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function set(Request $request): RedirectResponse
    {
        $target = Target::find($request->target);
        $target->update($request->all());

        return redirect()->route('home');
    }

    public function delete(Target $target): RedirectResponse
    {
        $target->delete();

        return redirect()->route('home');
    }
}
