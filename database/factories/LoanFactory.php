<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => \App\Models\Vehicle::factory(),
            'user_id' => \App\Models\User::factory(),
            'destination' => fake()->city(),
            'purpose' => fake()->sentence(),
            'start_mileage' => fake()->numberBetween(1000, 50000),
            'loan_date' => now()->subHours(fake()->numberBetween(1, 48)),
            'status' => 'active',
        ];
    }
}
