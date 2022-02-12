<?php

namespace Database\Seeders;

use App\Models\User;
use Log;
use Illuminate\Database\Seeder;
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
        User::firstOrCreate(
            ['email' => env('USER_EMAIL')],
            [
                'email' => env('USER_EMAIL'),
                'password' => Hash::make(env('PASSWORD')),
            ]
        );
    }
}
