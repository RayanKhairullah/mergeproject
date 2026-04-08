<?php

declare(strict_types=1);

use App\Enums\MeetingStatus;
use App\Models\Meeting;
use App\Models\Room;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::create(['name' => 'admin']);
});

test('admin can export meetings to excel', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $room = Room::factory()->create();
    Meeting::factory()->count(5)->create([
        'room_id' => $room->id,
        'status' => MeetingStatus::PUBLISHED,
    ]);

    $this->actingAs($admin);

    $response = Livewire::test(\App\Livewire\Admin\Meetings\Index::class)
        ->call('exportExcel');

    expect($response)->not->toBeNull();
});

test('admin can export meetings to pdf', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $room = Room::factory()->create();
    Meeting::factory()->count(5)->create([
        'room_id' => $room->id,
        'status' => MeetingStatus::PUBLISHED,
    ]);

    $this->actingAs($admin);

    $response = Livewire::test(\App\Livewire\Admin\Meetings\Index::class)
        ->call('exportPdf');

    expect($response)->not->toBeNull();
});

test('exported excel contains correct meeting data', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $room = Room::factory()->create(['name' => 'Test Room']);
    $meeting = Meeting::factory()->create([
        'title' => 'Test Meeting',
        'room_id' => $room->id,
        'status' => MeetingStatus::PUBLISHED,
        'created_by' => $admin->id,
    ]);

    $export = new \App\Exports\MeetingsExport(collect([$meeting]));
    $data = $export->map($meeting);

    expect($data[1])->toBe('Test Meeting')
        ->and($data[2])->toBe('Test Room');
});
