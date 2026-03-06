<div class="p-6 sm:p-8">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tighter text-gray-950 dark:text-white mb-2">
            Laporan Peminjaman
        </h1>
        <p class="text-base text-zinc-600 dark:text-zinc-400">
            Data dan riwayat peminjaman kendaraan operasional
        </p>
    </div>

    {{-- Filters --}}
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="lg:col-span-2">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                placeholder="Cari kendaraan atau peminjam..." 
                icon="magnifying-glass"
            />
        </div>
        <flux:select wire:model.live="vehicleFilter">
            <flux:select.option value="">Semua Kendaraan</flux:select.option>
            @foreach($vehicles as $vehicle)
                <flux:select.option value="{{ $vehicle->id }}">{{ $vehicle->license_plate }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:select wire:model.live="statusFilter">
            <flux:select.option value="">Semua Status</flux:select.option>
            <flux:select.option value="active">Sedang Dipinjam</flux:select.option>
            <flux:select.option value="returned">Sudah Dikembalikan</flux:select.option>
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
        <flux:button href="{{ route('vehicles.loan') }}" variant="primary" icon="plus">
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">KM</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Kondisi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Catatan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Foto Kilometer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Nama Peminjam</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Dibuat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($loans as $loan)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $loan->id }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $loan->vehicle->license_plate }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                <div>{{ $loan->loan_date->format('d/m/Y H:i') }}</div>
                                @if($loan->return_date)
                                    <div class="text-xs text-green-600 dark:text-green-400">
                                        Kembali: {{ $loan->return_date->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                <div>Awal: {{ number_format($loan->start_mileage) }}</div>
                                @if($loan->end_mileage)
                                    <div>Akhir: {{ number_format($loan->end_mileage) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($loan->return_date)
                                    <flux:badge color="green" size="sm">Dikembalikan</flux:badge>
                                @else
                                    <flux:badge color="yellow" size="sm">Dipinjam</flux:badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                <div class="max-w-xs truncate">{{ $loan->purpose }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($loan->speedometer_photo_url)
                                    <a href="{{ Storage::url($loan->speedometer_photo_url) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <flux:icon.photo class="w-5 h-5" />
                                    </a>
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $loan->user->name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $loan->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <flux:modal.trigger name="loan-detail-{{ $loan->id }}">
                                        <flux:button size="sm" variant="ghost" icon="eye">
                                            Detail
                                        </flux:button>
                                    </flux:modal.trigger>
                                    <flux:button 
                                        wire:click="delete({{ $loan->id }})" 
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
                        <flux:modal name="loan-detail-{{ $loan->id }}" class="min-w-[90vw] md:min-w-[600px] space-y-6">
                            <div>
                                <flux:heading size="lg">Detail Peminjaman #{{ $loan->id }}</flux:heading>
                                <flux:subheading>Informasi lengkap peminjaman kendaraan</flux:subheading>
                            </div>

                            <div class="space-y-4">
                                {{-- Vehicle Info --}}
                                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Informasi Kendaraan</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Plat Nomor:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $loan->vehicle->license_plate }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Status:</span>
                                            <p class="font-medium">
                                                @if($loan->return_date)
                                                    <flux:badge color="green" size="sm">Dikembalikan</flux:badge>
                                                @else
                                                    <flux:badge color="yellow" size="sm">Dipinjam</flux:badge>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Borrower Info --}}
                                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Informasi Peminjam</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Nama:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $loan->user->name }}</p>
                                        </div>
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Email:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $loan->user->email }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Loan Details --}}
                                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Detail Peminjaman</h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">Tanggal Pinjam:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $loan->loan_date->format('d M Y, H:i') }}</p>
                                        </div>
                                        @if($loan->return_date)
                                            <div>
                                                <span class="text-zinc-600 dark:text-zinc-400">Tanggal Kembali:</span>
                                                <p class="font-medium text-zinc-900 dark:text-white">{{ $loan->return_date->format('d M Y, H:i') }}</p>
                                            </div>
                                        @endif
                                        <div>
                                            <span class="text-zinc-600 dark:text-zinc-400">KM Awal:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ number_format($loan->start_mileage) }} km</p>
                                        </div>
                                        @if($loan->end_mileage)
                                            <div>
                                                <span class="text-zinc-600 dark:text-zinc-400">KM Akhir:</span>
                                                <p class="font-medium text-zinc-900 dark:text-white">{{ number_format($loan->end_mileage) }} km</p>
                                            </div>
                                            <div class="col-span-2">
                                                <span class="text-zinc-600 dark:text-zinc-400">Total Jarak:</span>
                                                <p class="font-medium text-blue-600 dark:text-blue-400">{{ number_format($loan->end_mileage - $loan->start_mileage) }} km</p>
                                            </div>
                                        @endif
                                        <div class="col-span-2">
                                            <span class="text-zinc-600 dark:text-zinc-400">Tujuan:</span>
                                            <p class="font-medium text-zinc-900 dark:text-white">{{ $loan->purpose }}</p>
                                        </div>
                                        @if($loan->destination)
                                            <div class="col-span-2">
                                                <span class="text-zinc-600 dark:text-zinc-400">Destinasi:</span>
                                                <p class="font-medium text-zinc-900 dark:text-white">{{ $loan->destination }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Photos --}}
                                @if($loan->speedometer_photo_url)
                                    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4">
                                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Foto Kilometer</h3>
                                        <div class="relative group">
                                            <div class="w-full h-64 bg-zinc-100 dark:bg-zinc-800 rounded-lg overflow-hidden cursor-zoom-in" 
                                                 onclick="openImageModal('{{ Storage::url($loan->speedometer_photo_url) }}')">
                                                <img src="{{ Storage::url($loan->speedometer_photo_url) }}" 
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
                                Tidak ada data peminjaman
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $loans->links() }}
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
