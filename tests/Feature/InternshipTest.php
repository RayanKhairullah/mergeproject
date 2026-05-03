<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

it('blocks anonymous users from admin internships dashboard', function () {
    $this->get('/admin/internships')->assertRedirect('/login');
});

it('blocks normal users from admin internships dashboard', function () {
    Role::firstOrCreate(['name' => 'user']);
    $user = User::factory()->create();
    $user->assignRole('user');
    
    $this->actingAs($user)->get('/admin/internships')->assertForbidden();
});

it('blocks normal users from mentor dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get('/mentor/dashboard')->assertForbidden();
});

it('blocks normal users from intern dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get('/intern/dashboard')->assertForbidden();
});
