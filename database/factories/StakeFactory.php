<?php

namespace Database\Factories;

use App\Models\StakingPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StakeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'staking_plan_id' => StakingPlan::factory(),
            'amount' => fake()->randomFloat(2, 100, 50000),
            'status' => 'active',
        ];
    }
}
