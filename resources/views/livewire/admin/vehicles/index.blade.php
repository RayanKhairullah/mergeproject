<div class="p-6 sm:p-8">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tighter text-gray-950 dark:text-white mb-2">
            Master Data Kendaraan
        </h1>
        <p class="text-base text-zinc-600 dark:text-zinc-400">
            Kelola data kendaraan operasional
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
            <flux:select.option value="available">Tersedia</flux:select.option>
            <flux:select.option value="in_use">Sedang Digunakan</flux:select.option>
            <flux:select.option value="maintenance">Maintenance</flux:select.option>
        </flux:select>
        <flux:button href="{{ route('admin.vehicles.create') }}" variant="primary" icon="plus">
            Tambah Kendaraan
        </flux:button>
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Plat Nomor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Kilometer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Service Terakhir</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Foto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Aksi</th>
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
                                    <flux:badge color="green" size="sm">Tersedia</flux:badge>
                                @elseif($vehicle->status === 'in_use')
                                    <flux:badge color="yellow" size="sm">Digunakan</flux:badge>
                                @else
                                    <flux:badge color="red" size="sm">Maintenance</flux:badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ number_format($vehicle->current_mileage) }} km
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $vehicle->last_service_date?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($vehicle->image)
                                    <a href="{{ Storage::url($vehicle->image) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <flux:icon.photo class="w-5 h-5" />
                                    </a>
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <flux:button href="{{ route('admin.vehicles.edit', $vehicle) }}" size="sm" variant="ghost" icon="pencil">
                                        Edit
                                    </flux:button>
                                    <flux:button 
                                        wire:click="delete({{ $vehicle->id }})" 
                                        wire:confirm="Apakah Anda yakin ingin menghapus kendaraan ini?"
                                        size="sm" 
                                        variant="danger" 
                                        icon="trash"
                                    >
                                        Hapus
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada data kendaraan
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
</div>
