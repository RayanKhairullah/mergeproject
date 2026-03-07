<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'license_plate' => strtoupper(fake()->bothify('? #### ??')),
            'current_mileage' => fake()->numberBetween(5000, 100000),
            'status' => fake()->randomElement(['available', 'in_use', 'maintenance']),
            'last_service_date' => fake()->dateTimeBetween('-6 months', '-1 month'),
        ];
    }
}
