<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Meeting Room A',
                'Meeting Room B',
                'Conference Hall',
                'Board Room',
                'Executive Suite',
                'Training Room',
            ]),
            'capacity' => fake()->randomElement([10, 20, 30, 50, 100]),
        ];
    }
}
