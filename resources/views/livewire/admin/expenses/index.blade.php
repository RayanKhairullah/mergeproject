<div class="p-6 sm:p-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-zinc-950 dark:text-white">
                {{ __('expenses.title') }}
            </h1>
            <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 mt-1">
                {{ __('expenses.subtitle') }}
            </p>
        </div>
    </div>

    <div class="mb-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="sm:col-span-2 lg:col-span-2">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                placeholder="{{ __('expenses.search_placeholder') }}" 
                icon="magnifying-glass"
            />
        </div>
        <flux:select wire:model.live="vehicleFilter" placeholder="{{ __('expenses.all_vehicles') }}">
            <flux:select.option value="">{{ __('expenses.all_vehicles') }}</flux:select.option>
            @foreach($vehicles as $vehicle)
                <flux:select.option value="{{ $vehicle->id }}">{{ $vehicle->license_plate }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:select wire:model.live="typeFilter" placeholder="{{ __('expenses.all_statuses') ?? __('expenses.all_types') }}">
            <flux:select.option value="">{{ __('expenses.all_types') }}</flux:select.option>
            <flux:select.option value="BBM">{{ __('expenses.type_bbm') }}</flux:select.option>
            <flux:select.option value="E-Money">{{ __('expenses.type_emoney') }}</flux:select.option>
            <flux:select.option value="Parkir">{{ __('expenses.type_parkir') }}</flux:select.option>
            <flux:select.option value="Cuci Mobil">{{ __('expenses.type_cuci') }}</flux:select.option>
            <flux:select.option value="Lainnya">{{ __('expenses.type_lainnya') }}</flux:select.option>
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
        <flux:button href="{{ route('vehicles.expense') }}" variant="primary" icon="plus" class="w-full sm:w-auto">
            {{ __('expenses.input_new') }}
        </flux:button>
    </div>
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="hidden sm:table-cell px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('expenses.vehicle') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('expenses.type') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('expenses.nominal') }}</th>
                        <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('expenses.source') }}</th>
                        <th class="hidden lg:table-cell px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('expenses.notes') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('expenses.photo') }}</th>
                        <th class="hidden sm:table-cell px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('expenses.reporter') }}</th>
                        <th class="hidden sm:table-cell px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('expenses.created_at') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-right">{{ __('expenses.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="hidden sm:table-cell px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100 italic">
                                #{{ $expense->id }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $expense->vehicle->license_plate }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <flux:badge 
                                    :color="$expense->expense_type === 'BBM' ? 'blue' : ($expense->expense_type === 'Parkir' ? 'green' : 'zinc')" 
                                    size="sm"
                                >
                                    {{ $expense->expense_type }}
                                </flux:badge>
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-zinc-900 dark:text-white">
                                <span class="text-[10px] text-zinc-400">Rp</span> {{ number_format($expense->nominal, 0, ',', '.') }}
                            </td>
                            <td class="hidden md:table-cell px-4 py-3 text-xs text-zinc-600 dark:text-zinc-400">
                                <div>{{ str_replace('_', ' ', $expense->funding_source) }}</div>
                                @if($expense->fuel_type)
                                    <div class="text-[10px] font-bold text-zinc-400">{{ $expense->fuel_type }} ({{ $expense->fuel_liters }}L)</div>
                                @endif
                            </td>
                            <td class="hidden lg:table-cell px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                <div class="max-w-xs truncate">
                                    {{ $expense->notes ?? '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if(is_array($expense->documentation_photos) && count($expense->documentation_photos) > 0)
                                    <div class="flex gap-1">
                                        @foreach(array_slice($expense->documentation_photos, 0, 1) as $photo)
                                            <a href="{{ Storage::url($photo) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                <flux:icon.photo class="w-4 h-4" />
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="hidden sm:table-cell px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $expense->reporter_name ?? $expense->user->name }}
                            </td>
                            <td class="hidden sm:table-cell px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $expense->created_at->format('d/m/y') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex gap-2 justify-end">
                                    <flux:modal.trigger name="expense-detail-{{ $expense->id }}">
                                        <flux:button size="sm" variant="ghost" icon="eye">
                                            <span class="hidden md:inline">{{ __('expenses.detail') }}</span>
                                        </flux:button>
                                    </flux:modal.trigger>
                                    <flux:button 
                                        wire:click="delete({{ $expense->id }})" 
                                        wire:confirm="{{ __('expenses.delete_confirm') }}"
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
                        <flux:modal name="expense-detail-{{ $expense->id }}" class="w-full max-w-2xl space-y-6">
                            <div>
                                <flux:heading size="lg">{{ __('expenses.detail_title', ['id' => $expense->id]) }}</flux:heading>
                                <flux:subheading>{{ __('expenses.detail_subtitle') }}</flux:subheading>
                            </div>

                            <div class="space-y-4">
                                {{-- Vehicle Info --}}
                                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">{{ __('expenses.vehicle_info') }}</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('expenses.license_plate') }}:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $expense->vehicle->license_plate }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('expenses.activity_type') }}:</span>
                                            <p class="font-medium">
                                                <flux:badge 
                                                    :color="$expense->expense_type === 'BBM' ? 'blue' : ($expense->expense_type === 'Parkir' ? 'green' : 'zinc')" 
                                                    size="sm"
                                                >
                                                    {{ $expense->expense_type }}
                                                </flux:badge>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Reporter Info --}}
                                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">{{ __('expenses.reporter_info') }}</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('expenses.name') }}:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $expense->reporter_name ?? $expense->user->name }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('expenses.report_date') }}:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $expense->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Expense Details --}}
                                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">{{ __('expenses.cost_details') }}</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('expenses.nominal') }}:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">Rp {{ number_format($expense->nominal, 0, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ __('expenses.funding_source') }}:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ str_replace('_', ' ', $expense->funding_source) }}</p>
                                        </div>
                                        @if($expense->fuel_type)
                                            <div>
                                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('expenses.fuel_type') }}:</span>
                                                <p class="font-medium text-zinc-900 dark:text-white">{{ $expense->fuel_type }}</p>
                                            </div>
                                            <div>
                                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('expenses.liters') }}:</span>
                                                <p class="font-medium text-zinc-900 dark:text-white">{{ $expense->fuel_liters }} L</p>
                                            </div>
                                        @endif
                                        @if($expense->notes)
                                            <div class="col-span-2">
                                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('expenses.notes') }}:</span>
                                                <p class="font-medium text-zinc-900 dark:text-white">{{ $expense->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Photos --}}
                                @if(is_array($expense->documentation_photos) && count($expense->documentation_photos) > 0)
                                    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">{{ __('expenses.documentation_photos') }}</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($expense->documentation_photos as $key => $photo)
                                                <div>
                                                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-2">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                                                    <div class="relative group">
                                                        <div class="w-full h-64 bg-zinc-100 dark:bg-zinc-800 rounded-lg overflow-hidden cursor-zoom-in" 
                                                             onclick="openImageModal('{{ Storage::url($photo) }}')">
                                                            <img src="{{ Storage::url($photo) }}" 
                                                                 alt="{{ $key }}" 
                                                                 class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105">
                                                        </div>
                                                        <div class="absolute top-2 right-2 bg-black/50 text-white px-2 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                                            {{ __('expenses.click_to_zoom') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex gap-2 justify-end">
                                <flux:modal.close>
                                    <flux:button variant="ghost">{{ __('expenses.close') }}</flux:button>
                                </flux:modal.close>
                            </div>
                        </flux:modal>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                {{ __('expenses.no_expenses_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $expenses->links() }}
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
