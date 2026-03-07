<div class="p-6 sm:p-8">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tighter text-gray-950 dark:text-white mb-2">
            {{ __('vehicles.master_title') }}
        </h1>
        <p class="text-base text-zinc-600 dark:text-zinc-400">
            {{ __('vehicles.master_subtitle') }}
        </p>
    </div>

    {{-- Filters --}}
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                placeholder="{{ __('vehicles.search_license_plate') }}" 
                icon="magnifying-glass"
            />
        </div>
        <flux:select wire:model.live="statusFilter" class="sm:w-48">
            <flux:select.option value="">{{ __('vehicles.all_statuses') }}</flux:select.option>
            <flux:select.option value="available">{{ __('vehicles.status_available') }}</flux:select.option>
            <flux:select.option value="in_use">{{ __('vehicles.status_in_use') }}</flux:select.option>
            <flux:select.option value="maintenance">{{ __('vehicles.status_maintenance') }}</flux:select.option>
        </flux:select>
        <flux:modal.trigger name="vehicle-form">
            <flux:button wire:click="showCreateForm" variant="primary" icon="plus">
                {{ __('vehicles.add_vehicle') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('vehicles.id') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('vehicles.license_plate') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('vehicles.status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('vehicles.mileage') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('vehicles.last_service') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('vehicles.photo') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-right">{{ __('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($vehicles as $vehicle)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $vehicle->id }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $vehicle->license_plate }}
                            </td>
                            <td class="px-4 py-3">
                                @if($vehicle->status === 'available')
                                    <flux:badge color="green" size="sm">{{ __('vehicles.status_available') }}</flux:badge>
                                @elseif($vehicle->status === 'in_use')
                                    <flux:badge color="yellow" size="sm">{{ __('vehicles.status_in_use') }}</flux:badge>
                                @else
                                    <flux:badge color="red" size="sm">{{ __('vehicles.status_maintenance') }}</flux:badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ number_format($vehicle->current_mileage) }} km
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $vehicle->last_service_date?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400 italic">
                                @if($vehicle->image)
                                    <a href="{{ Storage::url($vehicle->image) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <flux:icon.photo class="w-5 h-5" />
                                    </a>
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2 justify-end">
                                    <flux:modal.trigger name="vehicle-form">
                                        <flux:button wire:click="showEditForm({{ $vehicle->id }})" size="sm" variant="ghost" icon="pencil">
                                            {{ __('global.edit') }}
                                        </flux:button>
                                    </flux:modal.trigger>
                                    <flux:button 
                                        wire:click="delete({{ $vehicle->id }})" 
                                        wire:confirm="{{ __('vehicles.delete_confirm') }}"
                                        size="sm" 
                                        variant="danger" 
                                        icon="trash"
                                    >
                                        {{ __('global.delete') }}
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                {{ __('vehicles.no_vehicles') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $vehicles->links() }}
    </div>

    <flux:modal name="vehicle-form" :show="$showForm" class="min-w-[26rem]">
        <form wire:submit.prevent="{{ $editingVehicleId ? 'update' : 'save' }}" class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">{{ $editingVehicleId ? __('vehicles.edit_vehicle') : __('vehicles.add_vehicle') }}</flux:heading>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <flux:field>
                    <flux:label>{{ __('vehicles.license_plate') }}</flux:label>
                    <flux:input wire:model="license_plate" placeholder="{{ __('vehicles.license_plate_placeholder') }}" />
                    <flux:error name="license_plate" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('vehicles.status') }}</flux:label>
                    <flux:select wire:model="status">
                        <flux:select.option value="available">{{ __('vehicles.status_available') }}</flux:select.option>
                        <flux:select.option value="in_use">{{ __('vehicles.status_in_use') }}</flux:select.option>
                        <flux:select.option value="maintenance">{{ __('vehicles.status_maintenance') }}</flux:select.option>
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('vehicles.current_mileage') }}</flux:label>
                    <flux:input type="number" wire:model="current_mileage" min="0" />
                    <flux:error name="current_mileage" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('vehicles.last_service_date_label') }}</flux:label>
                    <flux:input type="date" wire:model="last_service_date" />
                    <flux:error name="last_service_date" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('vehicles.vehicle_photo') }}</flux:label>
                    <flux:input type="file" wire:model="image" />
                    <flux:error name="image" />

                    @if($existingImage)
                        <div class="mt-2">
                            <p class="text-xs text-zinc-500">{{ __('vehicles.current_photo') }}</p>
                            <img src="{{ Storage::url($existingImage) }}" alt="Vehicle image" class="h-24 rounded-md" />
                        </div>
                    @endif
                </flux:field>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost" wire:click="cancelForm">{{ __('global.cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $editingVehicleId ? __('vehicles.update') : __('vehicles.save_button') }}</span>
                    <span wire:loading>{{ $editingVehicleId ? __('vehicles.updating') : __('vehicles.saving') }}</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
