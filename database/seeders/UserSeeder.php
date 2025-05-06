<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'هیئت نظارت ۱',
            'email' => 'op1@example.com',
            'password' => Hash::make('password1'),
            'is_operator' => true,
        ]);

        User::create([
            'name' => 'هیئت نظارت ۲',
            'email' => 'op2@example.com',
            'password' => Hash::make('password2'),
            'is_operator' => true,
        ]);

        User::create([
            'name' => 'هیئت نظارت ۳',
            'email' => 'op3@example.com',
            'password' => Hash::make('password3'),
            'is_operator' => true,
        ]);

        User::create([
            'name' => 'رأی دهنده',
            'email' => 'vot@example.com',
            'password' => Hash::make('voter'),
            'is_voter' => true,
        ]);

        User::create([
            'name' => 'تایید کننده ۱',
            'email' => 'vrf1@example.com',
            'password' => Hash::make('password4'),
            'is_verifier' => true,
        ]);

        User::create([
            'name' => 'ادمین',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'),
            'is_operator' => true,
            'is_admin' => true,
            'is_verifier' => true,
        ]);
    }
}
