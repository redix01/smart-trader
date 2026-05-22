<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StakingPlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Flexible', 'Fixed 30d', 'Fixed 60d', 'Fixed 90d']),
            'currency' => 'USDT',
            'apy' => fake()->randomFloat(1, 3, 25),
            'payout_cycle' => 'daily',
            'duration_days' => fake()->randomElement([14, 30, 60, 90]),
            'min_amount' => 100,
            'max_amount' => null,
            'is_active' => true,
        ];
    }
}
