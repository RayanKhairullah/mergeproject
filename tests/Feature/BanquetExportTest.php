<?php

declare(strict_types=1);

use App\Enums\BanquetStatus;
use App\Models\Banquet;
use App\Models\DiningVenue;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::create(['name' => 'admin']);
});

test('admin can export banquets to excel', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $venue = DiningVenue::factory()->create();
    Banquet::factory()->count(5)->create([
        'venue_id' => $venue->id,
        'status' => BanquetStatus::PUBLISHED,
    ]);

    $this->actingAs($admin);

    $response = Livewire::test(\App\Livewire\Admin\Banquets\Index::class)
        ->call('exportExcel');

    expect($response)->not->toBeNull();
});

test('admin can export banquets to pdf', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $venue = DiningVenue::factory()->create();
    Banquet::factory()->count(5)->create([
        'venue_id' => $venue->id,
        'status' => BanquetStatus::PUBLISHED,
    ]);

    $this->actingAs($admin);

    $response = Livewire::test(\App\Livewire\Admin\Banquets\Index::class)
        ->call('exportPdf');

    expect($response)->not->toBeNull();
});

test('exported excel contains correct banquet data', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $venue = DiningVenue::factory()->create(['name' => 'Test Venue']);
    $banquet = Banquet::factory()->create([
        'title' => 'Test Banquet',
        'venue_id' => $venue->id,
        'status' => BanquetStatus::PUBLISHED,
        'created_by' => $admin->id,
    ]);

    $export = new \App\Exports\BanquetsExport(collect([$banquet]));
    $data = $export->map($banquet);

    expect($data[1])->toBe('Test Banquet')
        ->and($data[2])->toBe('Test Venue');
});

test('exported excel handles null cost correctly', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $venue = DiningVenue::factory()->create();
    $banquet = Banquet::factory()->create([
        'venue_id' => $venue->id,
        'cost' => null,
        'created_by' => $admin->id,
    ]);

    $export = new \App\Exports\BanquetsExport(collect([$banquet]));
    $data = $export->map($banquet);

    expect($data[6])->toBe('-');
});

test('exported excel formats cost correctly', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $venue = DiningVenue::factory()->create();
    $banquet = Banquet::factory()->create([
        'venue_id' => $venue->id,
        'cost' => 1500000,
        'created_by' => $admin->id,
    ]);

    $export = new \App\Exports\BanquetsExport(collect([$banquet]));
    $data = $export->map($banquet);

    expect($data[6])->toBe('Rp 1.500.000');
});
