<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MiningPlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Starter', 'Pro', 'Enterprise']).' Mining',
            'roi_percent' => fake()->randomFloat(1, 50, 500),
            'duration_days' => fake()->randomElement([30, 60, 90, 180]),
            'min_amount' => 100,
            'max_amount' => null,
            'is_active' => true,
        ];
    }
}
