@volt
<?php
use function Livewire\Volt\{state, computed, mount};

state([
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

mount(function () {
    $this->authorize('create meetings');
    $this->started_at = now()->addHour()->format('Y-m-d\TH:i');
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

$create = function () {
    $this->authorize('create meetings');
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
    $endedAt = $startedAt->copy()->addMinutes($this->duration);

    \App\Models\Meeting::create([
        'title' => $this->title,
        'notes' => $this->notes,
        'show_notes_on_monitor' => $this->show_notes_on_monitor,
        'show_on_monitor' => $this->show_on_monitor,
        'room_id' => $this->room_id,
        'started_at' => $startedAt,
        'ended_at' => $endedAt,
        'duration' => $this->duration,
        'estimated_participants' => $this->estimated_participants,
        'status' => \App\Enums\MeetingStatus::DRAFT,
        'created_by' => auth()->id(),
    ]);

    $this->dispatch('meeting-created');
    $this->dispatch('close-modal');
    
    // Reset form
    $this->title = '';
    $this->notes = '';
    $this->show_notes_on_monitor = false;
    $this->show_on_monitor = true;
    $this->room_id = null;
    $this->started_at = now()->addHour()->format('Y-m-d\TH:i');
    $this->duration = 60;
    $this->estimated_participants = 1;
};

$toggleCreateRoom = fn() => $this->showCreateRoom = !$this->showCreateRoom;
?>

<flux:modal name="create-meeting" class="w-full max-w-4xl">
    <form wire:submit="create" class="space-y-6">
        <div class="flex items-center justify-between">
            <flux:heading size="lg">{{ __('meetings.create') }}</flux:heading>
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
            <flux:textarea wire:model="notes" placeholder="{{ __('meetings.internal_notes') }}" rows="3" />
            <flux:error name="notes" />
        </flux:field>

        <div class="flex gap-3 justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('global.cancel') }}</flux:button>
            </flux:modal.close>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('meetings.publish') }}</span>
                <span wire:loading>{{ __('meetings.creating') }}</span>
            </flux:button>
        </div>
    </form>
</flux:modal>
@endvolt