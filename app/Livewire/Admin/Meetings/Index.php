<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Meetings;

use App\Enums\MeetingStatus;
use App\Models\Meeting;
use App\Models\Room;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $listeners = [
        'meeting-created' => '$refresh',
        'meeting-updated' => '$refresh',
        'room-created' => '$refresh',
    ];

    public string $search = '';

    public string $statusFilter = '';

    public string $roomFilter = '';

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

    public function updatedRoomFilter(): void
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

    public function publish(int $id): void
    {
        $meeting = Meeting::findOrFail($id);

        // Only allow publishing from DRAFT
        if ($meeting->status !== MeetingStatus::DRAFT) {
            return;
        }

        // If user has approve permission, they can publish directly to PUBLISHED
        // Otherwise, it goes to PENDING_APPROVAL
        if (auth()->user()->can('approve meetings')) {
            $meeting->update([
                'status' => MeetingStatus::PUBLISHED,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            session()->flash('success', __('meetings.success_updated'));
        } else {
            $meeting->update([
                'status' => MeetingStatus::PENDING_APPROVAL,
            ]);
            session()->flash('success', __('meetings.success_updated'));
        }
    }

    public function approve(int $id): void
    {
        if (! auth()->user()->can('approve meetings')) {
            session()->flash('error', __('meetings.error_no_permission_approve'));

            return;
        }

        $meeting = Meeting::findOrFail($id);

        if ($meeting->status !== MeetingStatus::PENDING_APPROVAL) {
            session()->flash('error', __('meetings.error_not_pending_approval'));

            return;
        }

        $meeting->update([
            'status' => MeetingStatus::PUBLISHED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        session()->flash('success', __('meetings.success_approved'));
    }

    public function reject(int $id, string $reason): void
    {
        if (! auth()->user()->can('approve meetings')) {
            session()->flash('error', __('meetings.error_no_permission_reject'));

            return;
        }

        $meeting = Meeting::findOrFail($id);

        if ($meeting->status !== MeetingStatus::PENDING_APPROVAL) {
            session()->flash('error', __('meetings.error_not_pending_approval'));

            return;
        }

        $meeting->update([
            'status' => MeetingStatus::REJECTED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);

        session()->flash('success', __('meetings.success_rejected'));
    }

    public function delete(int $id): void
    {
        $meeting = Meeting::findOrFail($id);

        // Check if user can delete
        if (! auth()->user()->can('delete meetings')) {
            session()->flash('error', __('meetings.error_no_permission_delete'));

            return;
        }

        if (! auth()->user()->can('approve meetings') && $meeting->created_by !== auth()->id()) {
            session()->flash('error', __('meetings.error_delete_only_own'));

            return;
        }

        // Regular users cannot delete approved meetings
        if (! auth()->user()->can('approve meetings') && $meeting->status !== \App\Enums\MeetingStatus::DRAFT && $meeting->status !== \App\Enums\MeetingStatus::PENDING_APPROVAL) {
            session()->flash('error', __('meetings.error_cannot_delete_approved'));

            return;
        }

        $meeting->delete();

        session()->flash('success', __('meetings.success_deleted'));
    }

    public function render()
    {
        $meetings = Meeting::query()
            ->with(['room', 'creator', 'approver'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->roomFilter, fn ($q) => $q->where('room_id', $this->roomFilter))
            ->when($this->dateFilter, fn ($q) => $q->whereDate('started_at', $this->dateFilter))
            ->orderBy('started_at', 'desc')
            ->paginate(10);

        $rooms = Room::all();
        $detailMeeting = $this->detailId ? Meeting::with(['room', 'creator', 'approver'])->find($this->detailId) : null;

        // Determine layout based on user role
        $layout = auth()->user()->hasRole(['admin', 'super-admin'])
            ? 'components.layouts.admin'
            : 'components.layouts.app.frontend';

        return view('livewire.admin.meetings.index', [
            'meetings' => $meetings,
            'rooms' => $rooms,
            'detailMeeting' => $detailMeeting,
        ])->title(__('sidebar.meeting'))->layout($layout);
    }
}
