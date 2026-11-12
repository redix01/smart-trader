<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Set ADMIN_EMAIL / ADMIN_PASSWORD in .env on the server before seeding.
        $email = env('ADMIN_EMAIL') ?: 'admin@nanomarketpro.net';
        $password = env('ADMIN_PASSWORD') ?: 'ADMINPASS123';

        $admin = User::where('email', '=', $email)->first();
        if($admin === null){
            User::create([
                'id' => Str::uuid(),
                'name' => 'Nano Market Pro Admin',
                'role' => 'admin',
                'status' => 'active',
                'balance' => 1000000, // Holding account
                'trading_balance' => 500000,
                'mining_balance' => 250000,
                'referral_balance' => 75000,
                'holding_balance' => 1000000,
                'staking_balance' => 300000,
                'profit' => 580000,
                'email' => $email,
                'email_verified_at' => \Carbon\Carbon::now(),
                'password' => Hash::make($password),
                'currency' => 'USD',
            ]);
        }
    }

}
