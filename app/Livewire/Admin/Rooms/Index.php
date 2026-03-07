<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Rooms;

use App\Models\Room;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $name = '';

    public int $capacity = 0;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1',
        ]);

        Room::create([
            'name' => $this->name,
            'capacity' => $this->capacity,
        ]);

        session()->flash('success', __('rooms.success_added'));
        $this->reset(['name', 'capacity']);
    }

    public function edit(int $id): void
    {
        $room = Room::findOrFail($id);
        $this->editingId = $id;
        $this->name = $room->name;
        $this->capacity = $room->capacity;
    }

    public function update(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1',
        ]);

        $room = Room::findOrFail($this->editingId);
        $room->update([
            'name' => $this->name,
            'capacity' => $this->capacity,
        ]);

        session()->flash('success', __('rooms.success_updated'));
        $this->reset(['editingId', 'name', 'capacity']);
    }

    public function delete(int $id): void
    {
        $room = Room::findOrFail($id);

        if ($room->activeMeetings()->count() > 0) {
            session()->flash('error', __('rooms.error_active_meetings'));

            return;
        }

        $room->delete();
        session()->flash('success', __('rooms.success_deleted'));
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingId', 'name', 'capacity']);
    }

    #[Layout('components.layouts.admin')]
    public function render()
    {
        $rooms = Room::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount('activeMeetings')
            ->paginate(10);

        return view('livewire.admin.rooms.index', [
            'title' => __('sidebar.meeting_rooms'),
            'rooms' => $rooms,
        ]);
    }
}
