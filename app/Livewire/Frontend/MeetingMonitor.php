<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Enums\MeetingStatus;
use App\Models\Meeting;
use App\Models\Room;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MeetingMonitor extends Component
{
    public ?int $roomFilter = null;

    #[Layout('components.layouts.app.frontend')]
    public function render()
    {
        $currentMeeting = Meeting::query()
            ->with(['room', 'creator'])
            ->where('status', MeetingStatus::PUBLISHED)
            ->where('show_on_monitor', true)
            ->where('started_at', '<=', now())
            ->where('ended_at', '>=', now())
            ->when($this->roomFilter, fn ($q) => $q->where('room_id', $this->roomFilter))
            ->orderBy('started_at')
            ->first();

        $upcomingMeetings = Meeting::query()
            ->with(['room', 'creator'])
            ->where('status', MeetingStatus::PUBLISHED)
            ->where('show_on_monitor', true)
            ->where('started_at', '>', now())
            ->when($this->roomFilter, fn ($q) => $q->where('room_id', $this->roomFilter))
            ->orderBy('started_at')
            ->take(5)
            ->get();

        $rooms = Room::all();

        return view('livewire.frontend.meeting-monitor', [
            'title' => __('global.monitor_rapat'),
            'currentMeeting' => $currentMeeting,
            'upcomingMeetings' => $upcomingMeetings,
            'rooms' => $rooms,
        ]);
    }
}
