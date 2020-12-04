<?php

namespace App\Http\Controllers;

use App\Http\LaravelLogReader;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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
        $data = $this->getLogs(request())->original;

        if ($data['success'] === true) {
            $logs = $data['data']['logs'];
            $success = true;
        } else {
            $logs = [];
            $success = true;
        }

        return view('home')
            ->with('target', Target::first())
            ->with('logs', array_reverse($logs))
            ->with('success', $success);
    }

    public function getLogs(Request $request)
    {
        if ($request->has('date')) {
            return (new LaravelLogReader(['date' => $request->get('date')]))->get();
        } else {
            return (new LaravelLogReader())->get();
        }
    }

    public function postDelete(Request $request)
    {
        if ($request->has('filename')) {
            $file = 'logs/' . $request->get('filename');
            if (File::exists(storage_path($file))) {
                File::delete(storage_path($file));
                return ['success' => true, 'message' => 'Successfully deleted'];
            }
        }
        if ($request->has('clear')) {
            if ($request->get('clear') == true) {
                $files = glob(storage_path('logs/*.log'));

                array_map('unlink', array_filter($files));
                return ['success' => true, 'message' => 'All Successfully deleted'];
            }
        }
    }
}
