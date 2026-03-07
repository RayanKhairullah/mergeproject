<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard access
            'access dashboard',


            // User management
            'view users',
            'create users',
            'update users',
            'delete users',

            // Role management
            'view roles',
            'create roles',
            'update roles',
            'delete roles',

            // Permission management
            'view permissions',
            'create permissions',
            'update permissions',
            'delete permissions',

            // Vehicle management
            'view vehicles',
            'create vehicles',
            'update vehicles',
            'delete vehicles',

            // Loan management
            'view loans',
            'create loans',
            'update loans',
            'delete loans',

            // Inspection management
            'view inspections',
            'create inspections',
            'update inspections',
            'delete inspections',

            // Expense management
            'view expenses',
            'create expenses',
            'update expenses',
            'delete expenses',

            // Meeting management
            'view meetings',
            'create meetings',
            'update meetings',
            'delete meetings',
            'approve meetings',

            // Banquet management
            'view banquets',
            'create banquets',
            'update banquets',
            'delete banquets',
            'approve banquets',

            // Room management
            'view rooms',
            'create rooms',
            'update rooms',
            'delete rooms',

            // Dining venue management
            'view dining_venues',
            'create dining_venues',
            'update dining_venues',
            'delete dining_venues',

            // Book management
            'view books',
            'create books',
            'update books',
            'delete books',

            // Category management
            'view categories',
            'create categories',
            'update categories',
            'delete categories',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate([
                'name' => $permission,
            ]);
        }
    }
}
