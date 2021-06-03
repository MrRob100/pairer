<?php

namespace App\Http\Controllers;

use App\Services\IEXGetService;
use Illuminate\Http\Request;

class OngController extends Controller
{
    public $iex;

    public function __construct(IEXGetService $IEXGetService)
    {
        $this->iex = $IEXGetService;
    }

    public function get(Request $request) {

        if ($request->symbol) {
            $data = array_reverse($this->iex->apiCall($request->symbol)['chart']);

            $total_inside = 0;
            $total_outside = 0;

            $all = [];

            $file = fopen($request->symbol . "-" . now() . ".csv", "w");

            for($i = 0; $i < sizeof($data) -1; $i++) {

//                dump($data[$i+1]['open']);

                $d_inside = $data[$i]['close'] - $data[$i]['open'];
                $d_outside = $data[$i+1]['open'] - $data[$i]['close'];



                $total_inside = $total_inside + $d_inside;
                $total_outside = $total_outside + $d_outside;

                $line = implode(',', $data[$i]);

                fputcsv($file, $data[$i]);
            }
            fclose($file);

            dump("inside hours: $total_inside");
            dd("outside hours: $total_outside");

        } else {
            return "no symbol";
        }
    }
}
