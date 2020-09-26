<?php

namespace Database\Seeders;

use Log;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $email = 'a@p.com';
        $admin = DB::table('users')->where('email', '=', $email)->first();

        if ($admin === null) {
            DB::table('users')->insert([
                'name' => 'ap',
                'email' => $email,
                'password' => Hash::make('cornharvest777'),
            ]);
        } else {
            $message = 'user record for ' . $email . ' already in db';
            Log::info($message);
            echo ($message) . "\n";
        }
    }
}
