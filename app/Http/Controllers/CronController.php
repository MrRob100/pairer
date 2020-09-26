<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

class CronController extends Controller
{
    public function run()
    {
        DB::table('test_bin')->insert([
            ['bin' => 'trash-' . rand()]
        ]);

        return 0;

    }

    public function check()
    {
        $check = App::make('App\Services\CheckService');

        $check->checkDB();
    }
}
