<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MeetingStatus;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingFactory extends Factory
{
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('+1 day', '+30 days');
        $duration = fake()->randomElement([30, 60, 90, 120]);

        return [
            'title' => fake()->sentence(4),
            'notes' => fake()->optional()->paragraph(),
            'show_notes_on_monitor' => fake()->boolean(30),
            'show_on_monitor' => fake()->boolean(80), // 80% chance to show on monitor
            'room_id' => Room::factory(),
            'started_at' => $startedAt,
            'ended_at' => (clone $startedAt)->modify("+{$duration} minutes"),
            'duration' => $duration,
            'estimated_participants' => fake()->numberBetween(5, 30),
            'status' => MeetingStatus::DRAFT,
            'created_by' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MeetingStatus::PUBLISHED,
            'approved_by' => User::factory(),
            'approved_at' => now(),
        ]);
    }

    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => now()->startOfHour(),
            'ended_at' => now()->startOfHour()->addHour(),
            'duration' => 60,
            'status' => MeetingStatus::PUBLISHED,
        ]);
    }
}
