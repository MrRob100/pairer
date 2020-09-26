<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CronController extends Controller
{
    public function run()
    {
        DB::table('test_bin')->insert([
            ['bin' => 'trash-' . rand()]
        ]);

        return 0;

    }
}
