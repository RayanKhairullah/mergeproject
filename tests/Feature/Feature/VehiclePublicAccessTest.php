<?php

use App\Livewire\Frontend\ExpenseForm;
use App\Livewire\Frontend\LoanForm;
use App\Livewire\Frontend\PublicReturnForm;
use App\Livewire\Frontend\VehicleMonitor;

test('guest can access vehicle monitor', function () {
    $response = $this->get(route('vehicles.monitor'));

    $response->assertSuccessful();
    $response->assertSeeLivewire(VehicleMonitor::class);
});

test('guest can access loan form', function () {
    $response = $this->get(route('vehicles.loan'));

    $response->assertSuccessful();
    $response->assertSeeLivewire(LoanForm::class);
});

test('guest can access public return form', function () {
    $response = $this->get(route('vehicles.return-public'));

    $response->assertSuccessful();
    $response->assertSeeLivewire(PublicReturnForm::class);
});

test('guest can access expense form', function () {
    $response = $this->get(route('vehicles.expense'));

    $response->assertSuccessful();
    $response->assertSeeLivewire(ExpenseForm::class);
});

test('guest cannot access inspection form', function () {
    $response = $this->get(route('vehicles.inspection'));

    $response->assertForbidden();
});
