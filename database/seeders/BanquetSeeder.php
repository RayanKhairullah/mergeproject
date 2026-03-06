<?php

namespace Database\Seeders;

use App\Enums\BanquetStatus;
use App\Enums\GuestType as GuestTypeEnum;
use App\Models\Banquet;
use App\Models\DiningVenue;
use App\Models\User;
use Illuminate\Database\Seeder;

class BanquetSeeder extends Seeder
{
    public function run(): void
    {
        $venues = DiningVenue::all();
        $users = User::all();
        $sdmUser = User::whereHas('roles', fn ($q) => $q->where('name', 'sdm'))->first();

        if ($venues->isEmpty() || $users->isEmpty()) {
            return;
        }

        $banquets = [
            [
                'title' => 'Jamuan Tamu VVIP Kementerian',
                'description' => 'Makan siang dengan delegasi kementerian',
                'guest_type' => GuestTypeEnum::VVIP,
                'venue_id' => $venues->random()->id,
                'scheduled_at' => now()->addDays(3)->setTime(12, 0),
                'estimated_guests' => 20,
                'cost' => 15000000,
                'status' => BanquetStatus::PUBLISHED,
                'created_by' => $users->random()->id,
                'approved_by' => $sdmUser?->id,
                'approved_at' => now(),
            ],
            [
                'title' => 'Makan Siang Rapat Direksi',
                'description' => 'Jamuan untuk rapat direksi bulanan',
                'guest_type' => GuestTypeEnum::VIP,
                'venue_id' => $venues->random()->id,
                'scheduled_at' => now()->addDays(2)->setTime(12, 30),
                'estimated_guests' => 15,
                'cost' => 8000000,
                'status' => BanquetStatus::PENDING_APPROVAL,
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'Coffee Break Workshop',
                'description' => 'Snack dan minuman untuk peserta workshop',
                'guest_type' => GuestTypeEnum::INTERNAL,
                'venue_id' => $venues->random()->id,
                'scheduled_at' => now()->addDays(7)->setTime(10, 0),
                'estimated_guests' => 30,
                'cost' => 3000000,
                'status' => BanquetStatus::PUBLISHED,
                'created_by' => $users->random()->id,
                'approved_by' => $sdmUser?->id,
                'approved_at' => now(),
            ],
            [
                'title' => 'Jamuan Ulang Tahun Perusahaan',
                'description' => 'Perayaan HUT perusahaan ke-25',
                'guest_type' => GuestTypeEnum::INTERNAL,
                'venue_id' => $venues->random()->id,
                'scheduled_at' => now()->addDays(30)->setTime(18, 0),
                'estimated_guests' => 100,
                'cost' => 25000000,
                'status' => BanquetStatus::DRAFT,
                'created_by' => $users->random()->id,
            ],
        ];

        foreach ($banquets as $banquet) {
            Banquet::create($banquet);
        }
    }
}
