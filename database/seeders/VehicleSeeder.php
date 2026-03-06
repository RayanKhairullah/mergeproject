<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            ['license_plate' => 'B 1234 ABC', 'current_mileage' => 15000],
            ['license_plate' => 'B 5678 DEF', 'current_mileage' => 22000],
            ['license_plate' => 'B 9012 GHI', 'current_mileage' => 8500],
            ['license_plate' => 'B 3456 JKL', 'current_mileage' => 31000],
            ['license_plate' => 'B 7890 MNO', 'current_mileage' => 12000],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
