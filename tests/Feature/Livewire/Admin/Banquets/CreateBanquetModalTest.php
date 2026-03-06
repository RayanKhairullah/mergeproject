<?php

use Livewire\Volt\Volt;

it('can render', function () {
    $component = Volt::test('admin.banquets.create-banquet-modal');

    $component->assertSee('');
});
