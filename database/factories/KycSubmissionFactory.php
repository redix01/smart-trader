<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class KycSubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'id_document_type' => fake()->randomElement(['passport', 'drivers_license', 'national_id']),
            'status' => 'pending',
        ];
    }
}
