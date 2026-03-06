<?php

use App\Models\Banquet;
use App\Models\DiningVenue;
use App\Models\User;
use Livewire\Volt\Volt;

it('can render', function () {
    $user = User::factory()->create();
    $venue = DiningVenue::factory()->create();
    $banquet = Banquet::factory()->create([
        'venue_id' => $venue->id,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user);

    $component = Volt::test('admin.banquets.edit-banquet-modal', ['banquetId' => $banquet->id]);

    $component->assertSee('Edit Banquet');
});
