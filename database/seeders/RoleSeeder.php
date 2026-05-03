<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========================================
        // 1. SUPER ADMIN - Full Access
        // ========================================
        $superAdminRole = Role::query()->updateOrCreate(['name' => 'super-admin']);
        $allPermissions = Permission::all()->pluck('name')->toArray();
        $superAdminRole->syncPermissions($allPermissions);

        // ========================================
        // 2. ADMIN - All except User/Role/Permission Management
        // ========================================
        $adminRole = Role::query()->updateOrCreate(['name' => 'admin']);
        $adminPermissions = [
            'access dashboard',

            // Vehicle management
            'view vehicles', 'create vehicles', 'update vehicles', 'delete vehicles',

            // Loan management
            'view loans', 'create loans', 'update loans', 'delete loans',

            // Inspection management
            'view inspections', 'create inspections', 'update inspections', 'delete inspections',

            // Expense management
            'view expenses', 'create expenses', 'update expenses', 'delete expenses',

            // Meeting management (with approval)
            'view meetings', 'create meetings', 'update meetings', 'delete meetings', 'approve meetings',

            // Banquet management (with approval)
            'view banquets', 'create banquets', 'update banquets', 'delete banquets', 'approve banquets',

            // Room management
            'view rooms', 'create rooms', 'update rooms', 'delete rooms',

            // Dining venue management
            'view dining_venues', 'create dining_venues', 'update dining_venues', 'delete dining_venues',

            // Book management
            'view books', 'create books', 'update books', 'delete books',

            // Category management
            'view categories', 'create categories', 'update categories', 'delete categories',

            // Division management
            'view divisions', 'create divisions', 'update divisions', 'delete divisions',

            // Employee management
            'view employees', 'create employees', 'update employees', 'delete employees',

            // Org Section management
            'view org-sections', 'create org-sections', 'update org-sections', 'delete org-sections',
        ];
        $adminRole->syncPermissions($adminPermissions);

        // ========================================
        // 3. SDM - Meeting & Banquet Management with Approval
        // ========================================
        $sdmRole = Role::query()->updateOrCreate(['name' => 'sdm']);
        $sdmPermissions = [
            // Meeting management (with approval)
            'view meetings', 'create meetings', 'update meetings', 'delete meetings', 'approve meetings',

            // Banquet management (with approval)
            'view banquets', 'create banquets', 'update banquets', 'delete banquets', 'approve banquets',

            // Room management
            'view rooms', 'create rooms', 'update rooms', 'delete rooms',

            // Dining venue management
            'view dining_venues', 'create dining_venues', 'update dining_venues', 'delete dining_venues',
        ];
        $sdmRole->syncPermissions($sdmPermissions);

        // ========================================
        // 4. USER - Meeting & Banquet Management WITHOUT Approval
        // ========================================
        $userRole = Role::query()->updateOrCreate(['name' => 'user']);
        $userPermissions = [
            // Meeting management (NO approval, can only edit/delete if not approved)
            'view meetings', 'create meetings', 'update meetings', 'delete meetings',

            // Banquet management (NO approval, can only edit/delete if not approved)
            'view banquets', 'create banquets', 'update banquets', 'delete banquets',

            // Room management
            'view rooms', 'create rooms', 'update rooms', 'delete rooms',

            // Dining venue management
            'view dining_venues', 'create dining_venues', 'update dining_venues', 'delete dining_venues',
        ];
        $userRole->syncPermissions($userPermissions);
        // ========================================
        // 5. INTERN - Intern Management (Intern side)
        // ========================================
        $internRole = Role::query()->updateOrCreate(['name' => 'intern']);
        // Base intern permissions can be bound here

        // ========================================
        // 6. MENTOR - Intern Management (Mentor side)
        // ========================================
        $mentorRole = Role::query()->updateOrCreate(['name' => 'mentor']);
        // Mentor approval and viewing permissions can be bound here

        // ========================================
        // 7. HR ADMIN - Intern Management (Admin side)
        // ========================================
        $hrAdminRole = Role::query()->updateOrCreate(['name' => 'hr-admin']);
        // HR Admin permissions can be bound here

        // ========================================
        // Create Sample Users
        // ========================================
        $this->createUserWithRole('superadmin@example.com', 'Super Admin', 'super-admin');
        $this->createUserWithRole('hradmin@example.com', 'HR Admin', 'hr-admin');
        $this->createUserWithRole('mentor@example.com', 'Mentor', 'mentor');
        $this->createUserWithRole('intern@example.com', 'Intern User', 'intern');
    }

    private function createUserWithRole(string $email, string $name, string $roleName): void
    {
        $user = \App\Models\User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles([$roleName]);
    }
}
