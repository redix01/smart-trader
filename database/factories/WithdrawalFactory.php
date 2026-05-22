<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'method' => 'wallet',
            'amount' => fake()->randomFloat(2, 10, 10000),
            'fee' => 0,
            'net_amount' => function (array $a) { return $a['amount']; },
            'currency' => 'USD',
            'destination_details' => json_encode(['wallet_address' => fake()->regexify('0x[a-f0-9]{40}')]),
            'status' => 'pending',
        ];
    }
}
