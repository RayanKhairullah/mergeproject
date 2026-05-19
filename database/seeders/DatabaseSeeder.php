<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call seeders in order
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserExcelSeeder::class,
            VehicleSeeder::class,
            DiningVenueSeeder::class,
            RoomSeeder::class,
            GuestTypeSeeder::class,
            MeetingSeeder::class,
            BanquetSeeder::class,
            CategorySeeder::class,
            BookSeeder::class,
            ReviewSeeder::class,
            DivisionSeeder::class,
            EmployeeSeeder::class,
        ]);
    }
}
