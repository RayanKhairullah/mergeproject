<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Livewire\Livewire;

test('users can login with username', function () {
    $user = User::factory()->create([
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    Livewire::test(Login::class)
        ->set('login', 'testuser')
        ->set('password', 'password')
        ->call('login');

    $this->assertAuthenticated();
});

test('users can login with email', function () {
    $user = User::factory()->create([
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    Livewire::test(Login::class)
        ->set('login', 'test@example.com')
        ->set('password', 'password')
        ->call('login');

    $this->assertAuthenticated();
});

test('admin users can access admin dashboard', function () {
    $this->seed(\Database\Seeders\PermissionSeeder::class);
    $this->seed(\Database\Seeders\RoleSeeder::class);

    $admin = User::where('email', 'admin@example.com')->first();

    $this->actingAs($admin);

    $response = $this->get('/admin');
    $response->assertSuccessful();
});

test('regular users can access dashboard', function () {
    $this->seed(\Database\Seeders\PermissionSeeder::class);
    $this->seed(\Database\Seeders\RoleSeeder::class);

    $user = User::where('email', 'user@example.com')->first();

    $this->actingAs($user);

    $response = $this->get('/dashboard');
    $response->assertSuccessful();
});

test('registration route is disabled', function () {
    $response = $this->get('/register');
    $response->assertNotFound();
});
