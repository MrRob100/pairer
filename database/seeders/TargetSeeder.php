<?php

namespace Database\Seeders;

use Log;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = DB::table('targets')->first();

        if ($admin === null) {
            DB::table('users')->insert([
                'lower' => '1',
                'upper' => '5',
                'pair' => 'XMRBNB',
            ]);
        } else {
            Log::info('target record there when tried to seed');
        }
    }
}
