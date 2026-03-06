<div class="p-6 sm:p-8">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tighter text-gray-950 dark:text-white mb-2">
            Rupa-rupa
        </h1>
        <p class="text-base text-zinc-600 dark:text-zinc-400">
            Laporan kegiatan/biaya kendaraan operasional
        </p>
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
        <flux:select wire:model.live="typeFilter">
            <flux:select.option value="">Semua Tipe</flux:select.option>
            <flux:select.option value="BBM">BBM</flux:select.option>
            <flux:select.option value="E-Money">E-Money</flux:select.option>
            <flux:select.option value="Parkir">Parkir</flux:select.option>
            <flux:select.option value="Cuci Mobil">Cuci Mobil</flux:select.option>
            <flux:select.option value="Lainnya">Lainnya</flux:select.option>
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
        <flux:button href="{{ route('vehicles.expense') }}" variant="primary" icon="plus">
            Input Laporan Baru
        </flux:button>
    </div>
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Kendaraan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Nominal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Sumber Dana</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Detail</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Catatan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Documentation Photos</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Pelapor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Dibuat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $expense->id }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $expense->vehicle->license_plate }}
                            </td>
                            <td class="px-4 py-3">
                                <flux:badge 
                                    :color="$expense->expense_type === 'BBM' ? 'blue' : ($expense->expense_type === 'Parkir' ? 'green' : 'zinc')" 
                                    size="sm"
                                >
                                    {{ $expense->expense_type }}
                                </flux:badge>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                Rp {{ number_format($expense->nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-xs text-zinc-600 dark:text-zinc-400">
                                {{ str_replace('_', ' ', $expense->funding_source) }}
                            </td>
                            <td class="px-4 py-3 text-xs text-zinc-600 dark:text-zinc-400">
                                @if($expense->fuel_type)
                                    <div>{{ $expense->fuel_type }}</div>
                                    <div>{{ $expense->fuel_liters }} L</div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                <div class="max-w-xs truncate">
                                    {{ $expense->notes ?? '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($expense->documentation_photos && count($expense->documentation_photos) > 0)
                                    <div class="flex gap-1">
                                        @foreach(array_slice($expense->documentation_photos, 0, 2) as $photo)
                                            <a href="{{ Storage::url($photo) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                <flux:icon.photo class="w-5 h-5" />
                                            </a>
                                        @endforeach
                                        @if(count($expense->documentation_photos) > 2)
                                            <span class="text-xs text-zinc-500">+{{ count($expense->documentation_photos) - 2 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $expense->reporter_name ?? $expense->user->name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $expense->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <flux:modal.trigger name="expense-detail-{{ $expense->id }}">
                                        <flux:button size="sm" variant="ghost" icon="eye">
                                            Detail
                                        </flux:button>
                                    </flux:modal.trigger>
                                    <flux:button 
                                        wire:click="delete({{ $expense->id }})" 
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
                        <flux:modal name="expense-detail-{{ $expense->id }}" class="min-w-[90vw] md:min-w-[600px] space-y-6">
                            <div>
                                <flux:heading size="lg">Detail Expense #{{ $expense->id }}</flux:heading>
                                <flux:subheading>Informasi lengkap kegiatan/biaya kendaraan</flux:subheading>
                            </div>

                            <div class="space-y-4">
                                {{-- Vehicle Info --}}
                                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Informasi Kendaraan</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Plat Nomor:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $expense->vehicle->license_plate }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Tipe Kegiatan:</span>
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
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Informasi Pelapor</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Nama:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $expense->reporter_name ?? $expense->user->name }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Tanggal Laporan:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $expense->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Expense Details --}}
                                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Detail Biaya</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Nominal:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">Rp {{ number_format($expense->nominal, 0, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Sumber Dana:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ str_replace('_', ' ', $expense->funding_source) }}</p>
                                        </div>
                                        @if($expense->fuel_type)
                                            <div>
                                                <span class="text-zinc-600 dark:text-zinc-400">Jenis BBM:</span>
                                                <p class="font-medium text-zinc-900 dark:text-white">{{ $expense->fuel_type }}</p>
                                            </div>
                                            <div>
                                                <span class="text-zinc-600 dark:text-zinc-400">Jumlah Liter:</span>
                                                <p class="font-medium text-zinc-900 dark:text-white">{{ $expense->fuel_liters }} L</p>
                                            </div>
                                        @endif
                                        @if($expense->notes)
                                            <div class="col-span-2">
                                                <span class="text-zinc-600 dark:text-zinc-400">Catatan:</span>
                                                <p class="font-medium text-zinc-900 dark:text-white">{{ $expense->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Photos --}}
                                @if($expense->documentation_photos && count($expense->documentation_photos) > 0)
                                    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Dokumentasi Foto</h3>
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
                                                            Klik untuk zoom
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
                                    <flux:button variant="ghost">Tutup</flux:button>
                                </flux:modal.close>
                            </div>
                        </flux:modal>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada data expense
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
