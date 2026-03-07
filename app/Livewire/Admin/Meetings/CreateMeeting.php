<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Meetings;

use App\Enums\MeetingStatus;
use App\Models\Meeting;
use App\Models\Room;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CreateMeeting extends Component
{
    public string $title = '';

    public string $notes = '';

    public bool $show_notes_on_monitor = false;

    public ?int $room_id = null;

    public string $started_at = '';

    public int $duration = 60;

    public int $estimated_participants = 1;

    public function mount(): void
    {
        $this->authorize('create meetings');
        $this->started_at = now()->addHour()->format('Y-m-d\TH:i');
    }

    public function create(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'room_id' => 'required|exists:rooms,id',
            'started_at' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:480',
            'estimated_participants' => 'required|integer|min:1',
        ], [
            'started_at.after' => 'Waktu mulai harus di masa depan.',
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

        Meeting::create([
            'title' => $this->title,
            'notes' => $this->notes,
            'show_notes_on_monitor' => $this->show_notes_on_monitor,
            'room_id' => $this->room_id,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'duration' => $this->duration,
            'estimated_participants' => $this->estimated_participants,
            'status' => MeetingStatus::DRAFT,
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', __('meetings.success_created'));
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

        return view('livewire.admin.meetings.create-meeting', [
            'rooms' => $rooms,
        ])->title(__('meetings.create_meeting'))->layout($layout);
    }
}
