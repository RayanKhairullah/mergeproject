<?php

declare(strict_types=1);

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create roles if they don't exist
    Role::firstOrCreate(['name' => 'super-admin']);
    Role::firstOrCreate(['name' => 'operasi']);
    Role::firstOrCreate(['name' => 'sdm']);
    Role::firstOrCreate(['name' => 'hsse']);
    Role::firstOrCreate(['name' => 'komersial']);

    // Ensure dashboard permissions exist for tests
    Permission::firstOrCreate(['name' => 'access dashboard']);
    Permission::firstOrCreate(['name' => 'view users']);
    Permission::firstOrCreate(['name' => 'view roles']);
    Permission::firstOrCreate(['name' => 'view permissions']);
});

test('super admin user sees sidebar layout', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super-admin');
    $superAdmin->givePermissionTo('access dashboard');

    $response = $this->actingAs($superAdmin)->get('/admin');

    $response->assertSuccessful();
    // Check for sidebar-specific elements
    $response->assertSee('flux:sidebar');

    // Language + theme controls should be present in the sidebar
    $response->assertSee('Light Mode');
});

test('super admin user can see users menu group', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super-admin');
    $superAdmin->givePermissionTo(['access dashboard', 'view users', 'view roles', 'view permissions']);

    $response = $this->actingAs($superAdmin)->get('/admin');

    $response->assertSuccessful();
    $response->assertSee('Users');
});

test('standard role user sees navbar layout', function () {
    $standardUser = User::factory()->create();
    $standardUser->assignRole('operasi');
    $standardUser->givePermissionTo('access dashboard');

    $response = $this->actingAs($standardUser)->get('/admin');

    $response->assertSuccessful();
    // Check for navbar-specific elements
    $response->assertSee('flux:header');
});

test('standard role user cannot see users menu group', function () {
    $standardUser = User::factory()->create();
    $standardUser->assignRole('operasi');
    $standardUser->givePermissionTo(['access dashboard', 'view users', 'view roles', 'view permissions']);

    $response = $this->actingAs($standardUser)->get('/admin');

    $response->assertSuccessful();
    // Should not see Users heading in navbar
    $response->assertDontSee('heading="Users"');
});

test('all roles can access other menu groups', function () {
    $roles = ['super-admin', 'operasi', 'sdm', 'hsse', 'komersial'];

    foreach ($roles as $roleName) {
        $user = User::factory()->create();
        $user->assignRole($roleName);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertSuccessful();
        $response->assertSee('Platform');
        $response->assertSee('Master Data');
        $response->assertSee('Digital Library');
        $response->assertSee('Laporan Kendaraan');
        $response->assertSee('Meeting & Banquet');
    }
});

test('layout resolver correctly determines layout based on role', function () {
    // Test super-admin gets sidebar
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super-admin');

    $response = $this->actingAs($superAdmin)->get('/admin');
    $response->assertSuccessful();

    // Test standard role gets navbar
    $standardUser = User::factory()->create();
    $standardUser->assignRole('sdm');

    $response = $this->actingAs($standardUser)->get('/admin');
    $response->assertSuccessful();
});

test('mobile menu works for navbar layout', function () {
    $standardUser = User::factory()->create();
    $standardUser->assignRole('operasi');

    $response = $this->actingAs($standardUser)->get('/admin');

    $response->assertSuccessful();
    $response->assertSee('mobile-menu-toggle');
    $response->assertSee('mobile-menu');
});


