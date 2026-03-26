<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Inspection;
use App\Models\Loan;
use App\Models\Meeting;
use App\Models\Room;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleExpense;
use Illuminate\Database\Seeder;

class SimulationSeeder extends Seeder
{
    /**
     * Run the database seeds to simulate a populated application.
     */
    public function run(): void
    {
        // 1. Ensure we have users
        if (User::count() === 0) {
            User::factory(10)->create();
        }
        $users = User::all();

        // 2. Ensure we have rooms for meetings
        if (Room::count() === 0) {
            Room::factory(5)->create();
        }
        $rooms = Room::all();

        // 3. Vehicles for monitor and forms
        $vehicles = Vehicle::factory(10)->create();

        // 4. Meetings for monitor (Current and Future)
        Meeting::factory(3)->today()->create([
            'room_id' => fn () => $rooms->random()->id,
            'created_by' => fn () => $users->random()->id,
        ]);

        Meeting::factory(5)->published()->create([
            'room_id' => fn () => $rooms->random()->id,
            'created_by' => fn () => $users->random()->id,
        ]);

        // 5. Active Loans for Return Form
        // We set some vehicles to 'in_use'
        $inUseVehicles = $vehicles->random(3);
        foreach ($inUseVehicles as $v) {
            $v->update(['status' => 'in_use']);
            Loan::factory()->create([
                'vehicle_id' => $v->id,
                'user_id' => $users->random()->id,
                'status' => 'active',
            ]);
        }

        // 6. Categories and Books for Digital Library
        if (Category::count() === 0) {
            Category::factory(5)->create();
        }
        $categories = Category::all();

        Book::factory(15)->create([
            'category_id' => fn () => $categories->random()->id,
        ]);

        // 7. Historical Data for Admin Simulation
        // Loans: Historical (Returned)
        Loan::factory(20)->create([
            'vehicle_id' => fn () => $vehicles->random()->id,
            'user_id' => fn () => $users->random()->id,
            'status' => 'returned',
            'return_date' => fn () => now()->subDays(rand(1, 30)),
            'end_mileage' => fn ($attr) => Vehicle::find($attr['vehicle_id'])->current_mileage + rand(10, 200),
        ]);

        // Expenses: Varied types and dates
        VehicleExpense::factory(30)->create([
            'vehicle_id' => fn () => $vehicles->random()->id,
            'user_id' => fn () => $users->random()->id,
            'created_at' => fn () => now()->subDays(rand(1, 60)),
        ]);

        // Inspections: Regular history
        Inspection::factory(25)->create([
            'vehicle_id' => fn () => $vehicles->random()->id,
            'user_id' => fn () => $users->random()->id,
            'created_at' => fn () => now()->subDays(rand(1, 60)),
        ]);
    }
}
