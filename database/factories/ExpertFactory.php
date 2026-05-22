<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExpertFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'bio' => fake()->sentence(),
            'win_rate' => fake()->randomFloat(1, 50, 99),
            'total_volume' => fake()->randomFloat(2, 1000, 500000),
            'profit_share' => fake()->randomFloat(1, 10, 40),
            'status' => 'verified',
            'is_active' => true,
        ];
    }
}
