<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepositFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'deposit_method_id' => 1,
            'amount' => fake()->randomFloat(2, 10, 50000),
            'fee' => 0,
            'net_amount' => function (array $a) { return $a['amount']; },
            'currency' => 'USD',
            'status' => 'pending',
            'proof_path' => null,
        ];
    }
}
