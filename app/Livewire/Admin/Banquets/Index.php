<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Banquets;

use App\Enums\BanquetStatus;
use App\Models\Banquet;
use App\Models\DiningVenue;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $listeners = [
        'banquet-created' => '$refresh',
        'banquet-updated' => '$refresh',
        'venue-created' => '$refresh',
    ];

    public string $search = '';

    public string $statusFilter = '';

    public string $venueFilter = '';
    public string $guestTypeFilter = '';
    public string $dateFilter = '';

    public ?int $detailId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedVenueFilter(): void
    {
        $this->resetPage();
    }

    public function updatedGuestTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateFilter(): void
    {
        $this->resetPage();
    }

    public function showDetail(int $id): void
    {
        $this->detailId = $id;
    }

    public function closeDetail(): void
    {
        $this->detailId = null;
    }

    public function approve(int $id): void
    {
        if (! auth()->user()->can('approve banquets')) {
            session()->flash('error', __('banquets.error_no_permission_approve'));

            return;
        }

        $banquet = Banquet::findOrFail($id);

        if ($banquet->status !== BanquetStatus::PENDING_APPROVAL) {
            session()->flash('error', __('banquets.error_not_pending_approval'));

            return;
        }

        $banquet->update([
            'status' => BanquetStatus::PUBLISHED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        session()->flash('success', __('banquets.success_approved'));
    }

    public function reject(int $id, string $reason): void
    {
        if (! auth()->user()->can('approve banquets')) {
            session()->flash('error', __('banquets.error_no_permission_reject'));

            return;
        }

        $banquet = Banquet::findOrFail($id);

        if ($banquet->status !== BanquetStatus::PENDING_APPROVAL) {
            session()->flash('error', __('banquets.error_not_pending_approval'));

            return;
        }

        $banquet->update([
            'status' => BanquetStatus::REJECTED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);

        session()->flash('success', __('banquets.success_rejected'));
    }

    public function delete(int $id): void
    {
        $banquet = Banquet::findOrFail($id);

        // Check if user can delete
        if (! auth()->user()->can('delete banquets')) {
            session()->flash('error', __('banquets.error_no_permission_delete'));

            return;
        }

        if (! auth()->user()->can('approve banquets') && $banquet->created_by !== auth()->id()) {
            session()->flash('error', __('banquets.error_delete_only_own'));

            return;
        }

        // Regular users cannot delete approved banquets
        if (! auth()->user()->can('approve banquets') && $banquet->status !== BanquetStatus::DRAFT && $banquet->status !== BanquetStatus::PENDING_APPROVAL) {
            session()->flash('error', __('banquets.error_cannot_delete_approved'));

            return;
        }

        $banquet->delete();

        session()->flash('success', __('banquets.success_deleted'));
    }

    public function render()
    {
        $banquets = Banquet::query()
            ->with(['venue', 'creator', 'approver'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->venueFilter, fn ($q) => $q->where('venue_id', $this->venueFilter))
            ->when($this->guestTypeFilter, fn ($q) => $q->where('guest_type', $this->guestTypeFilter))
            ->when($this->dateFilter, fn ($q) => $q->whereDate('scheduled_at', $this->dateFilter))
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10);

        $venues = DiningVenue::all();
        $detailBanquet = $this->detailId ? Banquet::with(['venue', 'creator', 'approver'])->find($this->detailId) : null;

        // Determine layout based on user role
        $layout = auth()->user()->hasRole(['admin', 'super-admin'])
            ? 'components.layouts.admin'
            : 'components.layouts.app.frontend';

        return view('livewire.admin.banquets.index', [
            'title' => __('sidebar.banquet'),
            'banquets' => $banquets,
            'venues' => $venues,
            'detailBanquet' => $detailBanquet,
        ])->layout($layout);
    }
}
