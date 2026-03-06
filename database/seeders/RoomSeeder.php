<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            ['name' => 'Meeting Room A', 'capacity' => 20],
            ['name' => 'Meeting Room B', 'capacity' => 30],
            ['name' => 'Conference Hall', 'capacity' => 100],
            ['name' => 'Board Room', 'capacity' => 15],
            ['name' => 'Training Room', 'capacity' => 50],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
