<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Meetings;

use App\Models\Meeting;
use App\Models\Room;
use Livewire\Attributes\Layout;
use Livewire\Component;

class EditMeeting extends Component
{
    public Meeting $meeting;

    public string $title = '';

    public string $notes = '';

    public bool $show_notes_on_monitor = false;

    public ?int $room_id = null;

    public string $started_at = '';

    public int $duration = 60;

    public int $estimated_participants = 1;

    public function mount(Meeting $meeting): void
    {
        $this->meeting = $meeting;
        $this->title = $meeting->title;
        $this->notes = $meeting->notes ?? '';
        $this->show_notes_on_monitor = $meeting->show_notes_on_monitor;
        $this->room_id = $meeting->room_id;
        $this->started_at = $meeting->started_at?->format('Y-m-d\TH:i') ?? '';
        $this->duration = $meeting->duration;
        $this->estimated_participants = $meeting->estimated_participants;
    }

    public function update(): void
    {
        // Check if user can update
        if (! auth()->user()->can('update meetings')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengubah meeting!');
            $this->redirect(route('admin.meetings.index'), navigate: true);

            return;
        }

        // Regular users cannot edit approved meetings
        if (! auth()->user()->can('approve meetings') && $this->meeting->status !== \App\Enums\MeetingStatus::DRAFT && $this->meeting->status !== \App\Enums\MeetingStatus::PENDING_APPROVAL) {
            session()->flash('error', 'Anda tidak dapat mengubah meeting yang sudah disetujui!');
            $this->redirect(route('admin.meetings.index'), navigate: true);

            return;
        }

        $this->validate([
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'room_id' => 'required|exists:rooms,id',
            'started_at' => 'required|date',
            'duration' => 'required|integer|min:15|max:480',
            'estimated_participants' => 'required|integer|min:1',
        ], [
            'duration.min' => 'Durasi minimal 15 menit.',
            'duration.max' => 'Durasi maksimal 8 jam (480 menit).',
            'estimated_participants.min' => 'Minimal 1 peserta diperlukan.',
        ]);

        $room = Room::findOrFail($this->room_id);

        if ($this->estimated_participants > $room->capacity) {
            $this->addError('estimated_participants', "Estimasi peserta ({$this->estimated_participants}) melebihi kapasitas ruang ({$room->capacity}).");

            return;
        }

        $startedAt = \Carbon\Carbon::parse($this->started_at);
        $endedAt = $startedAt->copy()->addMinutes($this->duration);

        $this->meeting->update([
            'title' => $this->title,
            'notes' => $this->notes,
            'show_notes_on_monitor' => $this->show_notes_on_monitor,
            'room_id' => $this->room_id,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'duration' => $this->duration,
            'estimated_participants' => $this->estimated_participants,
        ]);

        session()->flash('success', 'Meeting berhasil diperbarui!');
        $this->redirect(route('admin.meetings.index'), navigate: true);
    }

    #[Layout('components.layouts.admin')]
    public function render()
    {
        $rooms = Room::all();

        // Determine layout based on user role
        $layout = auth()->user()->hasRole(['admin', 'super-admin'])
            ? 'components.layouts.admin'
            : 'components.layouts.app.frontend';

        return view('livewire.admin.meetings.edit-meeting', [
            'rooms' => $rooms,
        ])->layout($layout);
    }
}
