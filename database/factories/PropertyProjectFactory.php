<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement(['Luxury Tower', 'Green Valley', 'Urban Loft', 'Seaside Villas']),
            'region' => fake()->city().', '.fake()->country(),
            'description' => fake()->sentence(),
            'strategy' => fake()->randomElement(['Capital Growth', 'Income', 'Mixed-Use']),
            'min_investment' => fake()->randomFloat(2, 1000, 100000),
            'target_roi' => fake()->randomFloat(1, 5, 25),
            'status' => 'active',
            'is_active' => true,
        ];
    }
}
