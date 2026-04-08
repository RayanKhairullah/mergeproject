<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('meetings.edit') }}</flux:heading>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <form wire:submit="update" class="space-y-6">
                <flux:field>
                    <flux:label>{{ __('meetings.fields.title') }}</flux:label>
                    <flux:input wire:model="title" placeholder="{{ __('meetings.fields.title') }}" />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('meetings.fields.room') }}</flux:label>
                    <flux:select wire:model.live="room_id" placeholder="{{ __('meetings.select_room') }}">
                        @foreach($rooms as $room)
                            <flux:select.option value="{{ $room->id }}">
                                {{ $room->name }} ({{ __('meetings.room_capacity') }}: {{ $room->capacity }} {{ __('meetings.person') }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="room_id" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('meetings.fields.started_at') }}</flux:label>
                        <flux:input type="datetime-local" wire:model="started_at" />
                        <flux:error name="started_at" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('meetings.fields.duration') }}</flux:label>
                        <flux:input type="number" wire:model="duration" min="15" max="480" />
                        <flux:error name="duration" />
                        <flux:description>{{ __('meetings.duration_desc') ?? 'Minimal 15 menit, maksimal 480 menit (8 jam)' }}</flux:description>
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>{{ __('meetings.fields.estimated_participants') }}</flux:label>
                    <flux:input type="number" wire:model="estimated_participants" min="1" />
                    <flux:error name="estimated_participants" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('meetings.fields.notes') }}</flux:label>
                    <x-rich-text wire:model="notes" placeholder="{{ __('meetings.notes_placeholder') ?? 'Tambahkan catatan, agenda, atau detail meeting...' }}" />
                    <flux:error name="notes" />
                </flux:field>

                <flux:field>
                    <flux:checkbox wire:model="show_notes_on_monitor">
                        {{ __('meetings.notes_monitor') }}
                    </flux:checkbox>
                </flux:field>

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('meetings.edit') }}</span>
                        <span wire:loading>{{ __('meetings.updating') }}</span>
                    </flux:button>
                    <flux:button type="button" variant="ghost" href="{{ route('admin.meetings.index') }}" wire:navigate>
                        {{ __('global.cancel') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
