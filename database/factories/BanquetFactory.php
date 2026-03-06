<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BanquetStatus;
use App\Enums\GuestType;
use App\Models\DiningVenue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BanquetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'guest_type' => fake()->randomElement(GuestType::cases()),
            'venue_id' => DiningVenue::factory(),
            'scheduled_at' => fake()->dateTimeBetween('+1 day', '+60 days'),
            'estimated_guests' => fake()->numberBetween(20, 150),
            'status' => BanquetStatus::DRAFT,
            'created_by' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BanquetStatus::PUBLISHED,
            'approved_by' => User::factory(),
            'approved_at' => now(),
        ]);
    }
}
