@volt
<?php
use function Livewire\Volt\{state, computed, mount};

state([
    'meetingId' => null,
    'title' => '',
    'notes' => '',
    'show_notes_on_monitor' => false,
    'show_on_monitor' => true,
    'room_id' => null,
    'new_room_name' => '',
    'new_room_capacity' => 10,
    'started_at' => '',
    'duration' => 60,
    'estimated_participants' => 1,
    'showCreateRoom' => false,
]);

mount(function ($meetingId) {
    $this->meetingId = $meetingId;
    $meeting = \App\Models\Meeting::findOrFail($meetingId);
    $this->authorize('update meetings', $meeting);
    
    $this->title = $meeting->title;
    $this->notes = $meeting->notes;
    $this->show_notes_on_monitor = $meeting->show_notes_on_monitor;
    $this->show_on_monitor = $meeting->show_on_monitor;
    $this->room_id = $meeting->room_id;
    $this->started_at = $meeting->started_at?->format('Y-m-d\TH:i');
    $this->duration = $meeting->duration;
    $this->estimated_participants = $meeting->estimated_participants;
});

$rooms = computed(fn() => \App\Models\Room::orderBy('name')->get());

$createRoom = function () {
    $this->validate([
        'new_room_name' => 'required|string|max:255|unique:rooms,name',
        'new_room_capacity' => 'required|integer|min:1|max:1000',
    ], [
        'new_room_name.unique' => 'Nama ruang sudah ada.',
        'new_room_capacity.min' => 'Kapasitas minimal 1 orang.',
        'new_room_capacity.max' => 'Kapasitas maksimal 1000 orang.',
    ]);

    $room = \App\Models\Room::create([
        'name' => $this->new_room_name,
        'capacity' => $this->new_room_capacity,
    ]);

    $this->room_id = $room->id;
    $this->new_room_name = '';
    $this->new_room_capacity = 10;
    $this->showCreateRoom = false;
    
    $this->dispatch('room-created');
};

$update = function () {
    $meeting = \App\Models\Meeting::findOrFail($this->meetingId);
    $this->authorize('update meetings', $meeting);
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

    $room = \App\Models\Room::findOrFail($this->room_id);

    if ($this->estimated_participants > $room->capacity) {
        $this->addError('estimated_participants', "Estimasi peserta ({$this->estimated_participants}) melebihi kapasitas ruang ({$room->capacity}).");
        return;
    }

    $startedAt = \Carbon\Carbon::parse($this->started_at);
    $endedAt = $startedAt->copy()->addMinutes((int) $this->duration);

    $meeting = \App\Models\Meeting::findOrFail($this->meetingId);
    $meeting->update([
        'title' => $this->title,
        'notes' => $this->notes,
        'show_notes_on_monitor' => $this->show_notes_on_monitor,
        'show_on_monitor' => $this->show_on_monitor,
        'room_id' => $this->room_id,
        'started_at' => $startedAt,
        'ended_at' => $endedAt,
        'duration' => (int) $this->duration,
        'estimated_participants' => (int) $this->estimated_participants,
    ]);

    $this->dispatch('meeting-updated');
    session()->flash('success', __('meetings.success_updated'));
    $this->dispatch('close-modal');
};

$toggleCreateRoom = fn() => $this->showCreateRoom = !$this->showCreateRoom;
?>

<flux:modal name="edit-meeting-{{ $meetingId }}" class="w-full max-w-4xl" x-on:meeting-updated.window="$flux.modal('edit-meeting-{{ $meetingId }}').close()">
    <form wire:submit="update" class="space-y-6">
        <div class="flex items-center justify-between">
            <flux:heading size="lg">{{ __('meetings.edit') }}</flux:heading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('meetings.fields.title') }}</flux:label>
                    <flux:input wire:model="title" placeholder="{{ __('meetings.fields.title') }}" />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('meetings.fields.room') }}</flux:label>
                    <div class="flex gap-2">
                        <flux:select wire:model.live="room_id" placeholder="{{ __('meetings.select_room') }}" class="flex-1">
                            @foreach($this->rooms as $room)
                                <flux:select.option value="{{ $room->id }}">
                                    {{ $room->name }} ({{ $room->capacity }} {{ __('meetings.person') }})
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:button type="button" wire:click="toggleCreateRoom" variant="ghost" size="sm" icon="plus" />
                    </div>
                    <flux:error name="room_id" />
                </flux:field>

                @if($showCreateRoom)
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg space-y-3">
                        <flux:heading size="sm">{{ __('meetings.add_new_room') }}</flux:heading>
                        <flux:field>
                            <flux:label>{{ __('meetings.room_name') }}</flux:label>
                            <flux:input wire:model="new_room_name" placeholder="{{ __('meetings.room_name') }}" />
                            <flux:error name="new_room_name" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('meetings.room_capacity') }}</flux:label>
                            <flux:input type="number" wire:model="new_room_capacity" min="1" max="1000" />
                            <flux:error name="new_room_capacity" />
                        </flux:field>
                        <div class="flex gap-2">
                            <flux:button type="button" wire:click="createRoom" size="sm" variant="primary">
                                {{ __('meetings.add_room') }}
                            </flux:button>
                            <flux:button type="button" wire:click="toggleCreateRoom" size="sm" variant="ghost">
                                {{ __('global.cancel') }}
                            </flux:button>
                        </div>
                    </div>
                @endif

                <flux:field>
                    <flux:label>{{ __('meetings.fields.estimated_participants') }}</flux:label>
                    <flux:input type="number" wire:model="estimated_participants" min="1" />
                    <flux:error name="estimated_participants" />
                </flux:field>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('meetings.fields.started_at') }}</flux:label>
                    <flux:input type="datetime-local" wire:model="started_at" />
                    <flux:error name="started_at" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('meetings.fields.duration') }}</flux:label>
                    <flux:input type="number" wire:model="duration" min="15" max="480" />
                    <flux:error name="duration" />
                </flux:field>

                <div class="space-y-4">
                    <div>
                        <flux:checkbox wire:model="show_on_monitor">
                            {{ __('meetings.public_monitor') }}
                        </flux:checkbox>
                        <p class="text-xs text-gray-500 mt-1 ml-6">{{ __('meetings.public_monitor_desc') }}</p>
                    </div>
                    
                    <div>
                        <flux:checkbox wire:model="show_notes_on_monitor">
                            {{ __('meetings.notes_monitor') }}
                        </flux:checkbox>
                        <p class="text-xs text-gray-500 mt-1 ml-6">{{ __('meetings.notes_monitor_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <flux:field>
            <flux:label>{{ __('meetings.internal_notes') }}</flux:label>
            <x-rich-text wire:model="notes" placeholder="{{ __('meetings.internal_notes') }}" />
            <flux:error name="notes" />
        </flux:field>

        <div class="flex gap-3 justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('global.cancel') }}</flux:button>
            </flux:modal.close>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('global.update') }}</span>
                <span wire:loading>{{ __('global.updating') }}</span>
            </flux:button>
        </div>
    </form>
</flux:modal>
@endvolt