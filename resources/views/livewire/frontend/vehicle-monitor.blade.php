<section class="w-full min-h-screen bg-gradient-to-br from-zinc-50 to-zinc-100 dark:from-zinc-900 dark:to-zinc-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-4xl font-bold tracking-tighter text-balance text-gray-950 dark:text-white mb-2">
                {{ __('vehicles.monitor') }}
            </h1>
            <p class="text-lg text-zinc-600 dark:text-zinc-400">
                {{ __('vehicles.monitor_description') }}
            </p>
        </div>

        {{-- Filters --}}
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <flux:input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari plat nomor..." 
                    icon="magnifying-glass"
                />
            </div>
            <flux:select wire:model.live="statusFilter" class="sm:w-48">
                <flux:select.option value="">Semua Status</flux:select.option>
                <flux:select.option value="available">{{ __('vehicles.available') }}</flux:select.option>
                <flux:select.option value="in_use">{{ __('vehicles.in_use') }}</flux:select.option>
                <flux:select.option value="maintenance">{{ __('vehicles.maintenance') }}</flux:select.option>
            </flux:select>
        </div>

        {{-- Vehicle Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 items-start">
            @forelse($vehicles as $vehicle)
                <div wire:key="vehicle-{{ $vehicle->id }}" 
                     x-data="{ activeLoanOpen: false, inspectionOpen: false }"
                     class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-zinc-200 dark:border-zinc-700">
                    
                    {{-- Vehicle Image --}}
                    @if($vehicle->image)
                        <div class="aspect-video w-full overflow-hidden bg-zinc-100 dark:bg-zinc-900">
                            <img 
                                src="{{ Storage::url($vehicle->image) }}" 
                                alt="{{ $vehicle->license_plate }}"
                                class="w-full h-full object-cover"
                            />
                        </div>
                    @else
                        <div class="aspect-video w-full bg-gradient-to-br from-zinc-100 to-zinc-200 dark:from-zinc-800 dark:to-zinc-900 flex items-center justify-center">
                            <flux:icon.truck class="w-16 h-16 text-zinc-400 dark:text-zinc-600" />
                        </div>
                    @endif

                    {{-- Status Badge --}}
                    <div class="p-3 sm:p-4 border-b border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                                {{ $vehicle->license_plate }}
                            </h3>
                            @if($vehicle->status === 'available')
                                <flux:badge color="green" size="sm">{{ __('vehicles.available') }}</flux:badge>
                            @elseif($vehicle->status === 'in_use')
                                <flux:badge color="yellow" size="sm">{{ __('vehicles.in_use') }}</flux:badge>
                            @else
                                <flux:badge color="red" size="sm">{{ __('vehicles.maintenance') }}</flux:badge>
                            @endif
                        </div>
                    </div>

                    {{-- Vehicle Info --}}
                    <div class="p-3 sm:p-4 space-y-3">
                        <div class="flex items-center text-sm text-zinc-600 dark:text-zinc-400">
                            <flux:icon.chart-bar class="w-5 h-5 mr-2 flex-shrink-0" />
                            <span class="font-medium">{{ number_format($vehicle->current_mileage) }} km</span>
                        </div>

                        @if($vehicle->activeLoan->isNotEmpty())
                            <div class="mt-3">
                                <button 
                                    @click="activeLoanOpen = !activeLoanOpen"
                                    class="w-full p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800 text-left hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors duration-200"
                                >
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-semibold text-amber-900 dark:text-amber-200">
                                            Sedang Dipinjam
                                        </p>
                                        <flux:icon.chevron-down 
                                            class="w-4 h-4 text-amber-700 dark:text-amber-400 transition-transform duration-200"
                                            ::class="{ 'rotate-180': activeLoanOpen }"
                                        />
                                    </div>
                                </button>
                                <div 
                                    x-show="activeLoanOpen"
                                    x-collapse
                                    class="mt-2 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800"
                                >
                                    <div class="space-y-2">
                                        <div>
                                            <p class="text-xs text-amber-700 dark:text-amber-400">Peminjam:</p>
                                            <p class="text-sm font-medium text-amber-900 dark:text-amber-200">
                                                {{ $vehicle->activeLoan->first()->user->name ?? 'Unknown' }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-amber-700 dark:text-amber-400">Tujuan:</p>
                                            <p class="text-sm font-medium text-amber-900 dark:text-amber-200">
                                                {{ $vehicle->activeLoan->first()->destination }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-amber-700 dark:text-amber-400">Tanggal Pinjam:</p>
                                            <p class="text-sm font-medium text-amber-900 dark:text-amber-200">
                                                {{ $vehicle->activeLoan->first()->loan_date->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Last Loan Info - Simple Format --}}
                            @php
                                $lastLoan = $vehicle->loans->first();
                            @endphp
                            @if($lastLoan)
                                <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <p class="text-xs text-blue-700 dark:text-blue-400 mb-1">
                                        Terakhir Dipinjam
                                    </p>
                                    <p class="text-sm font-medium text-blue-900 dark:text-blue-200">
                                        {{ $lastLoan->user->name ?? 'Unknown' }} - {{ $lastLoan->return_date?->format('d M Y') }}
                                    </p>
                                </div>
                            @endif
                        @endif

                        {{-- Latest Inspection Info --}}
                        @php
                            $latestInspection = $vehicle->inspections->first();
                        @endphp
                        @if($latestInspection)
                            <div class="mt-3">
                                <button 
                                    @click="inspectionOpen = !inspectionOpen"
                                    class="w-full p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 text-left hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors duration-200"
                                >
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-semibold text-green-900 dark:text-green-200">
                                            Inspeksi Terakhir
                                        </p>
                                        <flux:icon.chevron-down 
                                            class="w-4 h-4 text-green-700 dark:text-green-400 transition-transform duration-200"
                                            ::class="{ 'rotate-180': inspectionOpen }"
                                        />
                                    </div>
                                </button>
                                <div 
                                    x-show="inspectionOpen"
                                    x-collapse
                                    class="mt-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800"
                                >
                                    <div class="space-y-2 text-xs text-green-800 dark:text-green-300">
                                        <div class="flex justify-between">
                                            <span>Ban:</span>
                                            <span class="font-medium">{{ Str::limit($latestInspection->tire_condition, 20) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Body:</span>
                                            <span class="font-medium">{{ Str::limit($latestInspection->body_condition, 20) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Kaca:</span>
                                            <span class="font-medium">{{ Str::limit($latestInspection->glass_condition, 20) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Waktu:</span>
                                            <span class="font-medium">{{ ucfirst($latestInspection->inspection_time) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Tanggal:</span>
                                            <span class="font-medium">{{ $latestInspection->created_at->format('d M Y') }}</span>
                                        </div>
                                        @if($latestInspection->additional_notes)
                                            <div class="pt-2 border-t border-green-200 dark:border-green-700">
                                                <p class="text-xs text-green-700 dark:text-green-400 mb-1">Catatan:</p>
                                                <p class="text-xs text-green-900 dark:text-green-200">{{ $latestInspection->additional_notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($vehicle->last_service_date)
                            <div class="text-xs text-zinc-500 dark:text-zinc-500">
                                Service terakhir: {{ $vehicle->last_service_date->format('d M Y') }}
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="p-3 sm:p-4 bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-200 dark:border-zinc-700">
                        <div class="flex gap-2">
                            @if($vehicle->isAvailable())
                                <flux:button 
                                    href="{{ route('vehicles.loan', $vehicle) }}" 
                                    variant="primary" 
                                    class="flex-1"
                                    icon="arrow-right"
                                    size="sm"
                                >
                                    Pinjam
                                </flux:button>
                            @else
                                <flux:button 
                                    variant="ghost" 
                                    class="flex-1" 
                                    disabled
                                    size="sm"
                                >
                                    Tidak Tersedia
                                </flux:button>
                            @endif
                            
                            <flux:button 
                                href="{{ route('vehicles.expense') }}" 
                                variant="outline" 
                                class="flex-1"
                                icon="currency-dollar"
                                size="sm"
                            >
                                Rupa-rupa
                            </flux:button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <flux:icon.exclamation-circle class="w-16 h-16 mx-auto text-zinc-400 dark:text-zinc-600 mb-4" />
                    <p class="text-lg text-zinc-600 dark:text-zinc-400">
                        Tidak ada kendaraan ditemukan
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $vehicles->links() }}
        </div>
    </div>
</section>
