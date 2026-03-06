<?php

namespace App\Livewire\Admin\Vehicles;

use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $vehicle = Vehicle::findOrFail($id);

        // Check if vehicle has active loans
        if ($vehicle->loans()->whereNull('return_date')->exists()) {
            session()->flash('error', 'Tidak dapat menghapus kendaraan yang sedang dipinjam');

            return;
        }

        // Delete vehicle image if exists
        if ($vehicle->image) {
            \Storage::disk('public')->delete($vehicle->image);
        }

        $vehicle->delete();

        session()->flash('success', 'Kendaraan berhasil dihapus');
    }

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        $vehicles = Vehicle::query()
            ->when($this->search, function ($query) {
                $query->where('license_plate', 'like', '%'.$this->search.'%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('license_plate')
            ->paginate(10);

        return view('livewire.admin.vehicles.index', [
            'vehicles' => $vehicles,
        ]);
    }
}
