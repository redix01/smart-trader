<?php

namespace Database\Factories;

use App\Models\MiningPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MiningSubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'mining_plan_id' => MiningPlan::factory(),
            'amount' => fake()->randomFloat(2, 100, 50000),
            'status' => 'active',
        ];
    }
}
