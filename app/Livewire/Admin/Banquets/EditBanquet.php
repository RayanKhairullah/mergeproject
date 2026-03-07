<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Banquets;

use App\Models\Banquet;
use App\Models\DiningVenue;
use Livewire\Attributes\Layout;
use Livewire\Component;

class EditBanquet extends Component
{
    public Banquet $banquet;

    public string $title = '';

    public string $description = '';

    public string $guest_type = '';

    public ?int $venue_id = null;

    public string $scheduled_at = '';

    public int $estimated_guests = 1;

    public ?float $cost = null;

    public function mount(Banquet $banquet): void
    {
        $this->authorize('update banquets', $banquet);
        $this->banquet = $banquet;
        $this->title = $banquet->title;
        $this->description = $banquet->description ?? '';
        $this->guest_type = $banquet->guest_type->value;
        $this->venue_id = $banquet->venue_id;
        $this->scheduled_at = $banquet->scheduled_at?->format('Y-m-d\TH:i') ?? '';
        $this->estimated_guests = $banquet->estimated_guests ?? 1;
        $this->cost = $banquet->cost ? (float) $banquet->cost : null;
    }

    public function update(): void
    {
        // Check if user can update
        if (! auth()->user()->can('update banquets')) {
            session()->flash('error', __('banquets.error_no_permission_update'));
            $this->redirect(route('admin.banquets.index'), navigate: true);

            return;
        }

        // Regular users cannot edit approved banquets
        if (! auth()->user()->can('approve banquets') && $this->banquet->status !== \App\Enums\BanquetStatus::DRAFT && $this->banquet->status !== \App\Enums\BanquetStatus::PENDING_APPROVAL) {
            session()->flash('error', __('banquets.error_cannot_update_approved'));
            $this->redirect(route('admin.banquets.index'), navigate: true);

            return;
        }

        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'guest_type' => 'required|in:VVIP,VIP,Internal',
            'venue_id' => 'required|exists:dining_venues,id',
            'scheduled_at' => 'required|date',
            'estimated_guests' => 'required|integer|min:1',
            'cost' => 'nullable|numeric|min:0',
        ], [
            'estimated_guests.min' => 'Minimal 1 tamu diperlukan.',
        ]);

        $venue = DiningVenue::findOrFail($this->venue_id);

        $this->banquet->update([
            'title' => $this->title,
            'description' => $this->description,
            'guest_type' => $this->guest_type,
            'venue_id' => $this->venue_id,
            'scheduled_at' => $this->scheduled_at,
            'estimated_guests' => $this->estimated_guests,
            'cost' => $this->cost,
        ]);

        session()->flash('success', __('banquets.success_updated'));
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

        return view('livewire.admin.banquets.edit-banquet', [
            'venues' => $venues,
        ])->title(__('banquets.edit_banquet'))->layout($layout);
    }
}
