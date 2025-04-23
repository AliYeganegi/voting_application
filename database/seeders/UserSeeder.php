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
            'name' => 'Operator 1',
            'email' => 'op1@example.com',
            'password' => Hash::make('password1'),
            'is_operator' => true,
        ]);

        User::create([
            'name' => 'Operator 2',
            'email' => 'op2@example.com',
            'password' => Hash::make('password2'),
            'is_operator' => true,
        ]);

        User::create([
            'name' => 'Operator 3',
            'email' => 'op3@example.com',
            'password' => Hash::make('password3'),
            'is_operator' => true,
        ]);

        // Create sample candidates (you can add more later or seed from Excel if needed)
        for ($i = 1; $i <= 20; $i++) {
            User::create([
                'name' => "Candidate $i",
                'email' => "cand$i@example.com",
                'password' => Hash::make('candidate'),
                'is_candidate' => true,
            ]);
        }
    }
}
