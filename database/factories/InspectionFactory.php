<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inspection>
 */
class InspectionFactory extends Factory
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
            'inspection_time' => fake()->randomElement(['morning', 'afternoon']),
            'tire_condition' => 'good',
            'body_condition' => 'good',
            'glass_condition' => 'good',
            'mileage_check' => fake()->numberBetween(10000, 50000),
            'speedometer_photo_url' => 'https://via.placeholder.com/640x480.png?text=Speedometer',
            'issue_photos' => [],
        ];
    }
}
