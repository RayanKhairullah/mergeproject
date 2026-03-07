<div class="w-full min-h-screen font-roboto" x-data="{ view: 'grid' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Control Bar --}}
        <div class="max-w-4xl mx-auto">
            <div class="sticky top-20 z-30 mb-8 p-4 bg-white/70 dark:bg-zinc-900/70 backdrop-blur-xl rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <flux:input 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="{{ __('global.search_placeholder_vehicle') }}" 
                        icon="magnifying-glass"
                        class="bg-zinc-100/50 dark:bg-zinc-800/50 border-0!"
                    />
                </div>
                <div class="flex gap-4">
                    <flux:select wire:model.live="statusFilter" class="w-44 bg-zinc-100/50 dark:bg-zinc-800/50 border-0!">
                        <flux:select.option value="">{{ __('global.all_status') }}</flux:select.option>
                        <flux:select.option value="available">{{ __('vehicles.available') }}</flux:select.option>
                        <flux:select.option value="in_use">{{ __('vehicles.in_use') }}</flux:select.option>
                        <flux:select.option value="maintenance">{{ __('vehicles.maintenance') }}</flux:select.option>
                    </flux:select>
                    <flux:button icon="arrow-path" wire:click="$refresh" variant="ghost" />
                </div>
            </div>
        </div>

        {{-- Vehicle Grid --}}
        <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8' : 'flex flex-col gap-4'">
            @forelse($vehicles as $vehicle)
                <div wire:key="vehicle-{{ $vehicle->id }}" 
                     class="group relative bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 overflow-hidden hover:shadow-2xl hover:shadow-zinc-200/50 dark:hover:shadow-black/50 transition-all duration-500 flex"
                     :class="view === 'list' ? 'flex-row items-center p-4' : 'flex-col'">
                    
                    {{-- Image Section --}}
                    <div :class="view === 'list' ? 'w-48 h-28 rounded-xl overflow-hidden shrink-0' : 'aspect-[16/10] w-full overflow-hidden'">
                        @if($vehicle->image)
                            <img src="{{ Storage::url($vehicle->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                        @else
                            <div class="w-full h-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                <flux:icon.truck class="w-12 h-12 text-zinc-300 dark:text-zinc-700" />
                            </div>
                        @endif
                        
                        {{-- Status Float --}}
                        <div class="absolute top-4 left-4 z-20">
                            @if($vehicle->status === 'available')
                                <span class="px-3 py-1 bg-emerald-500/90 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-widest rounded-full">{{ __('vehicles.available') }}</span>
                            @elseif($vehicle->status === 'in_use')
                                <span class="px-3 py-1 bg-amber-500/90 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-widest rounded-full">{{ __('vehicles.in_use') }}</span>
                            @else
                                <span class="px-3 py-1 bg-rose-500/90 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-widest rounded-full">{{ __('vehicles.maintenance') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Content Section --}}
                    <div class="flex-1 p-6 flex flex-col">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-zinc-900 dark:text-white mb-1 tracking-tight">
                                    {{ $vehicle->license_plate }}
                                </h3>
                                <p class="text-sm text-zinc-500 font-medium">{{ $vehicle->model ?? 'Official Vehicle' }}</p>
                            </div>
                            <div class="p-2 bg-zinc-50 dark:bg-zinc-800 rounded-xl">
                                <flux:icon.identification class="w-5 h-5 text-zinc-400" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                                <p class="text-[10px] uppercase tracking-widest text-zinc-400 font-bold mb-1">{{ __('vehicles.current_mileage') }}</p>
                                <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ number_format($vehicle->current_mileage) }} <span class="text-[10px] font-normal text-zinc-500">KM</span></p>
                            </div>
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                                <p class="text-[10px] uppercase tracking-widest text-zinc-400 font-bold mb-1">{{ __('vehicles.last_service') }}</p>
                                <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ $vehicle->inspections->first()?->created_at->format('d M') ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Dynamic Info --}}
                        <div class="mb-6 flex-1">
                            @if($vehicle->isAvailable())
                                <div class="flex items-center gap-2 py-2 px-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-900/30">
                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <span class="text-xs font-medium text-emerald-700 dark:text-emerald-400 italic">{{ __('vehicles.ready_for_assignment') }}</span>
                                </div>
                            @else
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-bold text-zinc-500">
                                            {{ substr($vehicle->activeLoan->first()?->user?->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200 truncate">{{ $vehicle->activeLoan->first()?->user?->name }}</p>
                                            <p class="text-[10px] text-zinc-500 truncate">{{ $vehicle->activeLoan->first()?->destination }}</p>
                                        </div>
                                    </div>
                                    <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-1 rounded-full overflow-hidden">
                                        <div class="bg-amber-500 h-full w-2/3"></div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-3">
                            <flux:button 
                                href="{{ $vehicle->isAvailable() ? route('vehicles.loan', $vehicle) : '#' }}" 
                                variant="{{ $vehicle->isAvailable() ? 'primary' : 'subtle' }}"
                                class="flex-1 rounded-xl! h-11 font-bold {{ $vehicle->isAvailable() ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900' : '' }}"
                                :disabled="!$vehicle->isAvailable()"
                            >
                                {{ $vehicle->isAvailable() ? __('vehicles.reserve_now') : __('vehicles.currently_active') }}
                            </flux:button>
                            <flux:button icon="information-circle" variant="subtle" class="rounded-xl! h-11 w-11 shrink-0" />
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20 grayscale opacity-50">
                    <img src="https://illustrations.popsy.co/gray/searching.svg" class="w-64 h-64 mb-6" />
                    <p class="text-xl font-bold text-zinc-400 tracking-tight">{{ __('global.no_books_found') }}</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-12 flex justify-center">
            <div class="bg-white dark:bg-zinc-900 p-2 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-800">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>
</div>
