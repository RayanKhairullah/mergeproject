<?php

declare(strict_types=1);

namespace App\Livewire\Admin\DiningVenues;

use App\Models\DiningVenue;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $name = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
        ]);

        DiningVenue::create([
            'name' => $this->name,
        ]);

        session()->flash('success', __('dining_venues.success_added'));
        $this->reset(['name']);
    }

    public function edit(int $id): void
    {
        $venue = DiningVenue::findOrFail($id);
        $this->editingId = $id;
        $this->name = $venue->name;
    }

    public function update(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
        ]);

        $venue = DiningVenue::findOrFail($this->editingId);
        $venue->update([
            'name' => $this->name,
        ]);

        session()->flash('success', __('dining_venues.success_updated'));
        $this->reset(['editingId', 'name']);
    }

    public function delete(int $id): void
    {
        $venue = DiningVenue::findOrFail($id);

        if ($venue->activeBanquets()->count() > 0) {
            session()->flash('error', __('dining_venues.error_active_banquets'));

            return;
        }

        $venue->delete();
        session()->flash('success', __('dining_venues.success_deleted'));
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingId', 'name']);
    }

    #[Layout('components.layouts.admin')]
    public function render()
    {
        $venues = DiningVenue::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount('activeBanquets')
            ->paginate(10);

        return view('livewire.admin.dining-venues.index', [
            'venues' => $venues,
        ])->title(__('sidebar.dining_venues'));
    }
}
