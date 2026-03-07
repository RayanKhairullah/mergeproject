<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleExpense>
 */
class VehicleExpenseFactory extends Factory
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
            'reporter_name' => fake()->name(),
            'expense_type' => fake()->randomElement(['BBM', 'E-Money', 'Parkir', 'Cuci Mobil', 'Lainnya']),
            'nominal' => fake()->numberBetween(50000, 500000),
            'funding_source' => 'UANG_MUKA',
            'documentation_photos' => [],
        ];
    }
}
