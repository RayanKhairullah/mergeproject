<div class="p-6 sm:p-8">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tighter text-gray-950 dark:text-white mb-2">
            Kesiapan Kendaraan
        </h1>
        <p class="text-base text-zinc-600 dark:text-zinc-400">
            Laporan kondisi kesiapan mobil harian (Pagi / Sore)
        </p>
    </div>

    {{-- Kilometer Terkini per Kendaraan Section - Card Layout --}}
    <div class="mb-6">
        <h2 class="text-base font-semibold text-zinc-900 dark:text-white mb-3">
            Kilometer Terkini per Kendaraan
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
            @foreach($vehicleMileages as $vehicle)
                <div class="bg-white dark:bg-zinc-800 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    {{-- License Plate --}}
                    <div class="flex items-center gap-1.5 mb-2">
                        <flux:icon.truck class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0" />
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white truncate">
                            {{ $vehicle['license_plate'] }}
                        </h3>
                    </div>
                    
                    {{-- Mileage --}}
                    <div class="mb-2">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-0.5">Kilometer</p>
                        <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                            {{ number_format($vehicle['current_mileage']) }}
                        </p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">km</p>
                    </div>
                    
                    {{-- Source Info --}}
                    <div class="pt-2 border-t border-zinc-200 dark:border-zinc-700">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Sumber</p>
                        <div class="flex items-center justify-between gap-1">
                            <flux:badge :color="$vehicle['source'] === 'Peminjaman' ? 'green' : ($vehicle['source'] === 'Inspeksi' ? 'blue' : 'zinc')" size="sm" class="text-xs">
                                {{ $vehicle['source'] }}
                            </flux:badge>
                            @if($vehicle['source_date'])
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $vehicle['source_date']->format('d/m') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="lg:col-span-2">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                placeholder="Cari kendaraan..." 
                icon="magnifying-glass"
            />
        </div>
        <flux:select wire:model.live="vehicleFilter">
            <flux:select.option value="">Semua Kendaraan</flux:select.option>
            @foreach($vehicles as $vehicle)
                <flux:select.option value="{{ $vehicle->id }}">{{ $vehicle->license_plate }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:select wire:model.live="timeFilter">
            <flux:select.option value="">Semua Waktu</flux:select.option>
            <flux:select.option value="morning">Pagi</flux:select.option>
            <flux:select.option value="afternoon">Sore</flux:select.option>
        </flux:select>
        <flux:input 
            wire:model.live="dateFilter" 
            type="date"
            placeholder="Filter Tanggal"
        />
    </div>

    <div class="mb-4 flex justify-between items-center">
        <div class="flex gap-2">
            <flux:button wire:click="downloadExcel" variant="outline" icon="arrow-down-tray">
                Download XLSX
            </flux:button>
            <flux:button wire:click="downloadPdf" variant="outline" icon="arrow-down-tray">
                Download PDF
            </flux:button>
        </div>
        <flux:button href="{{ route('vehicles.inspection') }}" variant="primary" icon="plus">
            Input Laporan Baru
        </flux:button>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Kendaraan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">KM</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Kondisi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Catatan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Speedometer Photo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Inspektor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Dibuat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($inspections as $inspection)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $inspection->id }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $inspection->vehicle->license_plate }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                <flux:badge :color="$inspection->inspection_time === 'morning' ? 'blue' : 'orange'" size="sm">
                                    {{ ucfirst($inspection->inspection_time) }}
                                </flux:badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ number_format($inspection->mileage_check) }} km
                            </td>
                            <td class="px-4 py-3 text-xs text-zinc-600 dark:text-zinc-400">
                                <div>Ban: {{ Str::limit($inspection->tire_condition, 15) }}</div>
                                <div>Body: {{ Str::limit($inspection->body_condition, 15) }}</div>
                                <div>Kaca: {{ Str::limit($inspection->glass_condition, 15) }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                <div class="max-w-xs truncate">
                                    {{ $inspection->additional_notes ?? '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($inspection->speedometer_photo_url)
                                    <a href="{{ Storage::url($inspection->speedometer_photo_url) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <flux:icon.photo class="w-5 h-5" />
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $inspection->user->name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $inspection->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <flux:modal.trigger name="inspection-detail-{{ $inspection->id }}">
                                        <flux:button size="sm" variant="ghost" icon="eye">
                                            Detail
                                        </flux:button>
                                    </flux:modal.trigger>
                                    <flux:button 
                                        wire:click="delete({{ $inspection->id }})" 
                                        wire:confirm="Apakah Anda yakin ingin menghapus laporan ini?"
                                        size="sm" 
                                        variant="danger" 
                                        icon="trash"
                                    >
                                        Hapus
                                    </flux:button>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Detail --}}
                        <flux:modal name="inspection-detail-{{ $inspection->id }}" class="min-w-[90vw] md:min-w-[600px] space-y-6">
                            <div>
                                <flux:heading size="lg">Detail Inspeksi #{{ $inspection->id }}</flux:heading>
                                <flux:subheading>Informasi lengkap inspeksi kesiapan kendaraan</flux:subheading>
                            </div>

                            <div class="space-y-4">
                                {{-- Vehicle Info --}}
                                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Informasi Kendaraan</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Plat Nomor:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $inspection->vehicle->license_plate }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Waktu Inspeksi:</span>
                                            <p class="font-medium">
                                                <flux:badge :color="$inspection->inspection_time === 'morning' ? 'blue' : 'orange'" size="sm">
                                                    {{ ucfirst($inspection->inspection_time) }}
                                                </flux:badge>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Inspector Info --}}
                                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Informasi Inspektor</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Nama:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $inspection->user->name }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Tanggal Inspeksi:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $inspection->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Inspection Details --}}
                                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Detail Inspeksi</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Kilometer:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ number_format($inspection->mileage_check) }} km</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Kondisi Ban:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $inspection->tire_condition }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Kondisi Body:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $inspection->body_condition }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Kondisi Kaca:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $inspection->glass_condition }}</p>
                                        </div>
                                        @if($inspection->additional_notes)
                                            <div class="col-span-2">
                                                <span class="text-zinc-600 dark:text-zinc-400">Catatan Tambahan:</span>
                                                <p class="font-medium text-zinc-900 dark:text-white">{{ $inspection->additional_notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Photo with Zoom --}}
                                @if($inspection->speedometer_photo_url)
                                    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Foto Speedometer</h3>
                                        <div class="relative group">
                                            <div class="w-full h-64 bg-zinc-100 dark:bg-zinc-800 rounded-lg overflow-hidden cursor-zoom-in" 
                                                 onclick="openImageModal('{{ Storage::url($inspection->speedometer_photo_url) }}')">
                                                <img src="{{ Storage::url($inspection->speedometer_photo_url) }}" 
                                                     alt="Speedometer" 
                                                     class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105">
                                            </div>
                                            <div class="absolute top-2 right-2 bg-black/50 text-white px-2 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                                Klik untuk zoom
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex gap-2 justify-end">
                                <flux:modal.close>
                                    <flux:button variant="ghost">Tutup</flux:button>
                                </flux:modal.close>
                            </div>
                        </flux:modal>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada data inspeksi
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
