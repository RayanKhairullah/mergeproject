<?php

use Livewire\Volt\Volt;

it('can render', function () {
    $component = Volt::test('admin.meetings.create-meeting-modal');

    $component->assertSee('');
});
