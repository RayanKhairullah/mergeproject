<?php

namespace App\Livewire\Frontend;

use App\Models\Loan;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

class LoanForm extends Component
{
    use LivewireAlert;

    public ?Vehicle $vehicle = null;

    #[Validate('required|exists:vehicles,id')]
    public ?int $vehicle_id = null;

    #[Validate('required|exists:users,id')]
    public ?int $user_id = null;

    public string $userSearch = '';

    #[Validate('required|string|max:255')]
    public string $purpose = '';

    #[Validate('required|string|max:255')]
    public string $destination = '';

    public function mount(?Vehicle $vehicle = null): void
    {
        if ($vehicle) {
            $this->vehicle = $vehicle;
            $this->vehicle_id = $vehicle->id;
        }
    }

    public function selectEmployee(int $userId, string $userName): void
    {
        $this->user_id = $userId;
        $this->userSearch = $userName;
    }

    public function clearEmployee(): void
    {
        $this->user_id = null;
        $this->userSearch = '';
    }

    public function submitLoan(): void
    {
        $this->validate();

        $vehicle = Vehicle::findOrFail($this->vehicle_id);

        if (! $vehicle->isAvailable()) {
            $this->alert('error', 'Kendaraan tidak tersedia untuk dipinjam');

            return;
        }

        // Get borrower name
        $borrowerName = \App\Models\User::find($this->user_id)?->name ?? 'Unknown';

        $loan = Loan::create([
            'vehicle_id' => $this->vehicle_id,
            'user_id' => $this->user_id,
            'purpose' => $this->purpose,
            'destination' => $this->destination,
            'start_mileage' => $vehicle->current_mileage,
            'status' => 'active',
        ]);

        // Update vehicle status
        $vehicle->update(['status' => 'in_use']);

        // Dispatch browser event to save loan to localStorage
        $this->dispatch('save-loan-to-cache', [
            'loanId' => $loan->id,
            'vehicleId' => $vehicle->id,
            'vehicleName' => $vehicle->license_plate,
            'borrowerName' => $borrowerName,
            'startMileage' => $loan->start_mileage,
            'loanedAt' => $loan->created_at->toIso8601String(),
        ]);

        $this->alert('success', __('vehicles.loan_success'));

        // Redirect to return form
        $this->redirect(route('vehicles.return'), true);
    }

    #[Layout('components.layouts.app.frontend')]
    public function render(): View
    {
        $vehicles = Vehicle::query()
            ->orderBy('license_plate')
            ->get();

        $users = \App\Models\User::query()
            ->orderBy('name')
            ->get();

        return view('livewire.frontend.loan-form', [
            'title' => __('global.peminjaman'),
            'vehicles' => $vehicles,
            'users' => $users,
        ]);
    }
}
