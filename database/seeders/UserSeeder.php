<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Gregor Koprivnikar',
            'email' => 'gregakop@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('gr33nt3ch77'),
            'remember_token' => Str::random(10),
        ]);
    }
}
