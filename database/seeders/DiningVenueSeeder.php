<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DiningVenue;
use Illuminate\Database\Seeder;

class DiningVenueSeeder extends Seeder
{
    public function run(): void
    {
        $venues = [
            ['name' => 'Grand Ballroom'],
            ['name' => 'Garden Terrace'],
            ['name' => 'Executive Dining Hall'],
            ['name' => 'VIP Lounge'],
        ];

        foreach ($venues as $venue) {
            DiningVenue::create($venue);
        }
    }
}
