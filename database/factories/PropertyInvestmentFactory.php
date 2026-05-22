<?php

namespace Database\Factories;

use App\Models\PropertyProject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyInvestmentFactory extends Factory
{
    public function definition(): array
    {
        $units = fake()->numberBetween(1, 10);
        return [
            'user_id' => User::factory(),
            'property_project_id' => PropertyProject::factory(),
            'units' => $units,
            'total_amount' => $units * 1000,
            'status' => 'active',
        ];
    }
}
