<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Banquets;

use App\Enums\BanquetStatus;
use App\Models\Banquet;
use App\Models\DiningVenue;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CreateBanquet extends Component
{
    public string $title = '';

    public string $description = '';

    public string $guest_type = '';

    public ?int $venue_id = null;

    public string $scheduled_at = '';

    public int $estimated_guests = 1;

    public ?float $cost = null;

    public function mount(): void
    {
        $this->authorize('create banquets');
        $this->scheduled_at = now()->addDay()->format('Y-m-d\TH:i');
    }

    public function create(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'guest_type' => 'required|in:VVIP,VIP,Internal',
            'venue_id' => 'required|exists:dining_venues,id',
            'scheduled_at' => 'required|date|after:now',
            'estimated_guests' => 'required|integer|min:1',
            'cost' => 'nullable|numeric|min:0',
        ], [
            'scheduled_at.after' => 'Tanggal terjadwal harus di masa depan.',
            'estimated_guests.min' => 'Minimal 1 tamu diperlukan.',
        ]);

        $venue = DiningVenue::findOrFail($this->venue_id);

        Banquet::create([
            'title' => $this->title,
            'description' => $this->description,
            'guest_type' => $this->guest_type,
            'venue_id' => $this->venue_id,
            'scheduled_at' => $this->scheduled_at,
            'estimated_guests' => $this->estimated_guests,
            'cost' => $this->cost,
            'status' => BanquetStatus::DRAFT,
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', __('banquets.success_created'));
        $this->redirect(route('admin.banquets.index'), navigate: true);
    }

    #[Layout('components.layouts.admin')]
    public function render()
    {
        $venues = DiningVenue::all();

        // Determine layout based on user role
        $layout = auth()->user()->hasRole(['admin', 'super-admin'])
            ? 'components.layouts.admin'
            : 'components.layouts.app.frontend';

        return view('livewire.admin.banquets.create-banquet', [
            'venues' => $venues,
        ])->layout($layout);
    }
}
