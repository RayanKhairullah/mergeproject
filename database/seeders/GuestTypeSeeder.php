<?php

namespace Database\Seeders;

use App\Models\GuestType;
use Illuminate\Database\Seeder;

class GuestTypeSeeder extends Seeder
{
    public function run(): void
    {
        $guestTypes = [
            ['value' => 'vvip', 'label' => 'VVIP'],
            ['value' => 'vip', 'label' => 'VIP'],
            ['value' => 'internal', 'label' => 'Internal'],
        ];

        foreach ($guestTypes as $type) {
            GuestType::query()->updateOrCreate(
                ['value' => $type['value']],
                $type
            );
        }
    }
}
