<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function download(Request $request)
    {
        $filePath = public_path("/" . $request->symbol . ".csv");
        $headers = ['Content-Type: application/csv'];
        $fileName = $request->symbol . "_" . now().'.csv';

        return response()->download($filePath, $fileName, $headers);
    }
}
