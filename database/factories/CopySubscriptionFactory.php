<?php

namespace Database\Factories;

use App\Models\Expert;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CopySubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'expert_id' => Expert::factory(),
            'amount' => fake()->randomFloat(2, 100, 50000),
            'status' => 'active',
        ];
    }
}
