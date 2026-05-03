<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InternSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample interns
        $interns = [
            ['name' => 'Budi Santoso', 'email' => 'budi.intern@pelindo.co.id'],
            ['name' => 'Siti Aminah', 'email' => 'siti.intern@pelindo.co.id'],
            ['name' => 'Agus Wijaya', 'email' => 'agus.intern@pelindo.co.id'],
            ['name' => 'Ani Lestari', 'email' => 'ani.intern@pelindo.co.id'],
        ];

        foreach ($interns as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles(['intern']);
        }

        // Create sample mentors (different from the one in RoleSeeder)
        $mentors = [
            ['name' => 'Eko Prasetyo', 'email' => 'eko.mentor@pelindo.co.id'],
            ['name' => 'Dewi Sartika', 'email' => 'dewi.mentor@pelindo.co.id'],
        ];

        foreach ($mentors as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles(['mentor']);
        }
    }
}
