<div class="p-6 sm:p-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-zinc-950 dark:text-white">
                {{ __('inspections.title') }}
            </h1>
            <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 mt-1">
                {{ __('inspections.subtitle') }}
            </p>
        </div>
    </div>

    {{-- Kilometer Terkini per Kendaraan Section - Horizontal Scroll for many items --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                <flux:icon.presentation-chart-line class="w-5 h-5 text-blue-600" />
                {{ __('inspections.current_mileage_title') }}
            </h2>
            <div class="hidden sm:flex gap-1">
                <span class="text-xs text-zinc-500 bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded-full flex items-center gap-1">
                    <flux:icon.arrows-right-left class="w-3 h-3" />
                    {{ __('inspections.scroll_tip') }}
                </span>
            </div>
        </div>
        
        <div class="relative group">
            <div class="flex flex-nowrap overflow-x-auto pb-4 gap-4 scrollbar-hide snap-x snap-mandatory -mx-6 px-6 sm:mx-0 sm:px-0">
                @foreach($vehicleMileages as $vehicle)
                    <div class="flex-none w-[200px] sm:w-[220px] snap-start">
                        <div class="h-full bg-white dark:bg-zinc-800/50 backdrop-blur-sm rounded-2xl p-4 border border-zinc-200 dark:border-zinc-700/50 shadow-sm hover:shadow-md hover:border-blue-500/50 transition-all duration-300 group/card">
                            {{-- License Plate --}}
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                                        <flux:icon.truck class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <h3 class="text-sm font-black text-zinc-900 dark:text-white tracking-tight">
                                        {{ $vehicle['license_plate'] }}
                                    </h3>
                                </div>
                                <flux:badge :color="$vehicle['source'] === 'Peminjaman' ? 'green' : ($vehicle['source'] === 'Inspeksi' ? 'blue' : 'zinc')" size="sm" class="text-[10px] uppercase font-bold px-1.5">
                                    {{ $vehicle['source'] }}
                                </flux:badge>
                            </div>
                            
                            {{-- Mileage --}}
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('inspections.odo_meter') }}</p>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-2xl font-black text-zinc-900 dark:text-white tabular-nums">
                                        {{ number_format($vehicle['current_mileage']) }}
                                    </span>
                                    <span class="text-xs font-bold text-zinc-400">KM</span>
                                </div>
                            </div>
                            
                            {{-- Footer Info --}}
                            <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-700/50 flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <flux:icon.clock class="w-3 h-3 text-zinc-400" />
                                    <span class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400">
                                        {{ $vehicle['source_date'] ? $vehicle['source_date']->diffForHumans(['short' => true]) : '-' }}
                                    </span>
                                </div>
                                @if($vehicle['source_date'])
                                    <span class="text-[10px] font-bold text-zinc-400">
                                        {{ $vehicle['source_date']->format('d/m') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- Fade Edges --}}
            <div class="absolute inset-y-0 left-0 w-8 bg-gradient-to-r from-zinc-50 dark:from-zinc-950 to-transparent pointer-events-none opacity-0 sm:opacity-100 sm:-left-4"></div>
            <div class="absolute inset-y-0 right-0 w-8 bg-gradient-to-l from-zinc-50 dark:from-zinc-950 to-transparent pointer-events-none opacity-0 sm:opacity-100 sm:-right-4"></div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="sm:col-span-2 lg:col-span-2">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                placeholder="{{ __('inspections.search_placeholder') }}" 
                icon="magnifying-glass"
            />
        </div>
        <flux:select wire:model.live="vehicleFilter" placeholder="{{ __('inspections.all_vehicles') }}">
            <flux:select.option value="">{{ __('inspections.all_vehicles') }}</flux:select.option>
            @foreach($vehicles as $vehicle)
                <flux:select.option value="{{ $vehicle->id }}">{{ $vehicle->license_plate }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:select wire:model.live="timeFilter" placeholder="{{ __('inspections.all_times') }}">
            <flux:select.option value="">{{ __('inspections.all_times') }}</flux:select.option>
            <flux:select.option value="morning">{{ __('inspections.morning') }}</flux:select.option>
            <flux:select.option value="afternoon">{{ __('inspections.afternoon') }}</flux:select.option>
        </flux:select>
        <flux:input 
            wire:model.live="dateFilter" 
            type="date"
        />
    </div>

    <div class="mb-4 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4">
        <div class="flex flex-col sm:flex-row gap-2">
            <flux:button wire:click="downloadExcel" variant="outline" icon="arrow-down-tray" class="w-full sm:w-auto">
                Download XLSX
            </flux:button>
            <flux:button wire:click="downloadPdf" variant="outline" icon="arrow-down-tray" class="w-full sm:w-auto">
                Download PDF
            </flux:button>
        </div>
        <flux:button href="{{ route('vehicles.inspection') }}" variant="primary" icon="plus" class="w-full sm:w-auto">
            {{ __('inspections.input_new') }}
        </flux:button>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="hidden sm:table-cell px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('inspections.vehicle') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('inspections.time') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('inspections.mileage') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('inspections.condition') }}</th>
                        <th class="hidden lg:table-cell px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('inspections.notes') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('inspections.photo') }}</th>
                        <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('inspections.inspector') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('inspections.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($inspections as $inspection)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="hidden sm:table-cell px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100 italic">
                                #{{ $inspection->id }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $inspection->vehicle->license_plate }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                <flux:badge :color="$inspection->inspection_time === 'morning' ? 'blue' : 'orange'" size="sm">
                                    {{ ucfirst($inspection->inspection_time) }}
                                </flux:badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100 font-bold">
                                {{ number_format($inspection->mileage_check) }} km
                            </td>
                            <td class="px-4 py-3 text-xs text-zinc-600 dark:text-zinc-400">
                                <div class="flex flex-col gap-0.5">
                                    <div class="flex items-center gap-1.5"><div class="w-1.5 h-1.5 rounded-full bg-zinc-400"></div>{{ __('inspections.body') }}: {{ Str::limit($inspection->body_condition, 10) }}</div>
                                    <div class="flex items-center gap-1.5"><div class="w-1.5 h-1.5 rounded-full bg-zinc-400"></div>{{ __('inspections.tire') }}: {{ Str::limit($inspection->tire_condition, 10) }}</div>
                                </div>
                            </td>
                            <td class="hidden lg:table-cell px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                <div class="max-w-xs truncate">
                                    {{ $inspection->additional_notes ?? '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($inspection->speedometer_photo_url)
                                    <a href="{{ Storage::url($inspection->speedometer_photo_url) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <flux:icon.photo class="w-4 h-4" />
                                    </a>
                                @endif
                            </td>
                            <td class="hidden md:table-cell px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $inspection->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:modal.trigger name="inspection-detail-{{ $inspection->id }}">
                                        <flux:button size="sm" variant="ghost" icon="eye">
                                            <span class="hidden md:inline">{{ __('inspections.detail') }}</span>
                                        </flux:button>
                                    </flux:modal.trigger>
                                    <flux:button 
                                        wire:click="delete({{ $inspection->id }})" 
                                        wire:confirm="{{ __('inspections.delete_confirm') }}"
                                        size="sm" 
                                        variant="danger" 
                                        icon="trash"
                                    >
                                        <span class="hidden md:inline">{{ __('global.delete') }}</span>
                                    </flux:button>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Detail --}}
                        <flux:modal name="inspection-detail-{{ $inspection->id }}" class="w-full max-w-2xl space-y-6">
                            <div>
                                <flux:heading size="lg">{{ __('inspections.detail_title', ['id' => $inspection->id]) }}</flux:heading>
                                <flux:subheading>{{ __('inspections.detail_subtitle') }}</flux:subheading>
                            </div>

                            <div class="space-y-4">
                                {{-- Vehicle Info --}}
                                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">{{ __('inspections.vehicle_info') }}</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('inspections.license_plate') }}:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $inspection->vehicle->license_plate }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('inspections.inspection_time') }}:</span>
                                            <p class="font-medium">
                                                <flux:badge :color="$inspection->inspection_time === 'morning' ? 'blue' : 'orange'" size="sm">
                                                    {{ $inspection->inspection_time === 'morning' ? __('inspections.morning') : __('inspections.afternoon') }}
                                                </flux:badge>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Inspector Info --}}
                                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">{{ __('inspections.inspector_info') }}</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('inspections.name') }}:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $inspection->user->name }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('inspections.inspection_date') }}:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $inspection->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Inspection Details --}}
                                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">{{ __('inspections.inspection_details') }}</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('inspections.mileage') }}:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ number_format($inspection->mileage_check) }} km</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('inspections.tire_condition') }}:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $inspection->tire_condition }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('inspections.body_condition') }}:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $inspection->body_condition }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('inspections.glass_condition') }}:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $inspection->glass_condition }}</p>
                                        </div>
                                        @if($inspection->additional_notes)
                                            <div class="col-span-2">
                                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('inspections.additional_notes') }}:</span>
                                                <p class="font-medium text-zinc-900 dark:text-white">{{ $inspection->additional_notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Photo with Zoom --}}
                                @if($inspection->speedometer_photo_url)
                                    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">{{ __('inspections.speedometer_photo') }}</h3>
                                        <div class="relative group">
                                            <div class="w-full h-64 bg-zinc-100 dark:bg-zinc-800 rounded-lg overflow-hidden cursor-zoom-in" 
                                                 onclick="openImageModal('{{ Storage::url($inspection->speedometer_photo_url) }}')">
                                                <img src="{{ Storage::url($inspection->speedometer_photo_url) }}" 
                                                     alt="Speedometer" 
                                                     class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105">
                                            </div>
                                            <div class="absolute top-2 right-2 bg-black/50 text-white px-2 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                                {{ __('inspections.click_to_zoom') }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex gap-2 justify-end">
                                <flux:modal.close>
                                    <flux:button variant="ghost">{{ __('inspections.close') }}</flux:button>
                                </flux:modal.close>
                            </div>
                        </flux:modal>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                {{ __('inspections.no_inspections_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $inspections->links() }}
    </div>

    {{-- Image Zoom Modal --}}
    <div id="imageZoomModal" class="hidden fixed inset-0 z-[9999] bg-black/90 flex items-center justify-center p-4" onclick="closeImageModal()">
        <div class="relative max-w-7xl max-h-full">
            <button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white hover:text-zinc-300 text-4xl font-light">&times;</button>
            <img id="zoomedImage" src="" alt="Zoomed" class="max-w-full max-h-[90vh] object-contain">
        </div>
    </div>

    <script>
        function openImageModal(imageUrl) {
            event.stopPropagation();
            document.getElementById('zoomedImage').src = imageUrl;
            document.getElementById('imageZoomModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageZoomModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</div>
