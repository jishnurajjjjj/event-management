<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'sanju',
            'email' => 'sanju@gmail.com',
            'password' => Hash::make('123123123'),
        ]);

        User::create([
            'name' => 'jishnu',
            'email' => 'jishnu@gmail.com',
            'password' => Hash::make('123123123'),
        ]);

        User::create([
            'name' => 'abhi',
            'email' => 'abhi@gmail.com',
            'password' => Hash::make('123123123'),
        ]);
    }
}
