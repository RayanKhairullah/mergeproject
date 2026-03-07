<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('rooms.title') }}</flux:heading>
    </div>

    <div class="flex gap-4 items-end">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('rooms.search_placeholder') }}" class="flex-1" />
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <flux:heading size="lg">{{ $editingId ? __('rooms.edit_title') : __('rooms.add_new') }}</flux:heading>
        </div>
        <div class="p-6">
            <form wire:submit="{{ $editingId ? 'update' : 'create' }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('rooms.name') }}</flux:label>
                        <flux:input wire:model="name" placeholder="{{ __('rooms.name_placeholder') }}" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('rooms.capacity') }}</flux:label>
                        <flux:input type="number" wire:model="capacity" placeholder="{{ __('rooms.capacity_placeholder') }}" min="1" />
                        <flux:error name="capacity" />
                    </flux:field>
                </div>

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? __('global.save') : __('rooms.add_new') }}
                    </flux:button>
                    @if($editingId)
                        <flux:button type="button" variant="ghost" wire:click="cancelEdit">
                            {{ __('global.cancel') }}
                        </flux:button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('rooms.name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('rooms.capacity') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('rooms.active_meetings') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('rooms.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($rooms as $room)
                        <tr wire:key="room-{{ $room->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $room->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $room->capacity }} {{ __('rooms.capacity_unit') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $room->active_meetings_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <flux:button size="sm" wire:click="edit({{ $room->id }})">
                                    {{ __('global.edit') }}
                                </flux:button>
                                <flux:button size="sm" variant="danger" wire:click="delete({{ $room->id }})" wire:confirm="{{ __('rooms.delete_confirm') }}">
                                    {{ __('global.delete') }}
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('rooms.no_rooms_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $rooms->links() }}
        </div>
    </div>
</div>
