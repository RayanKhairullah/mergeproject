<?php

namespace App\Livewire\Frontend;

use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleMonitor extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    public ?Vehicle $selectedVehicle = null;

    public function openDetails(int $vehicleId): void
    {
        $this->selectedVehicle = Vehicle::with(['activeLoan.user', 'loans' => function ($query) {
            $query->where('status', 'returned')->latest('return_date')->limit(1)->with('user');
        }, 'inspections' => function ($query) {
            $query->latest()->limit(1);
        }])->find($vehicleId);
    }

    public function closeDetails(): void
    {
        $this->selectedVehicle = null;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    #[Layout('components.layouts.app.frontend')]
    public function render(): View
    {
        $vehicles = Vehicle::query()
            ->with(['activeLoan.user', 'loans' => function ($query) {
                $query->where('status', 'returned')
                    ->latest('return_date')
                    ->limit(1)
                    ->with('user');
            }, 'inspections' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->when($this->search, function ($query) {
                $query->where('license_plate', 'like', "%{$this->search}%");
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('license_plate')
            ->paginate(12);

        return view('livewire.frontend.vehicle-monitor', [
            'vehicles' => $vehicles,
        ])->title(__('global.monitor_mobil'));
    }
}
