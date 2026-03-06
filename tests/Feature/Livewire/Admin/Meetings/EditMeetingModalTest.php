<?php

use App\Models\Meeting;
use App\Models\Room;
use App\Models\User;
use Livewire\Volt\Volt;

it('can render', function () {
    $user = User::factory()->create();
    $room = Room::factory()->create();
    $meeting = Meeting::factory()->create([
        'room_id' => $room->id,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user);

    $component = Volt::test('admin.meetings.edit-meeting-modal', ['meetingId' => $meeting->id]);

    $component->assertSee('Edit Meeting');
});
