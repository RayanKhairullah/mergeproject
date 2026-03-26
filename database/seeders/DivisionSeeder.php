<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            'Direksi',
            'Teknologi Informasi',
            'Keuangan',
            'Operasional',
            'Komersial',
            'Sumber Daya Manusia (SDM)',
            'HSSE',
        ];

        foreach ($divisions as $name) {
            Division::query()->updateOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)]
            );
        }
    }
}
