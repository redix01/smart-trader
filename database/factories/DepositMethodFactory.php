<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DepositMethodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'currency' => 'USDT',
            'network' => 'TRC20',
            'label' => 'USDT (TRC20)',
            'wallet_address' => fake()->sha256(),
            'min_amount' => 10,
            'max_amount' => 100000,
            'fee_fixed' => 0,
            'fee_percent' => 0,
            'is_active' => true,
        ];
    }
}
