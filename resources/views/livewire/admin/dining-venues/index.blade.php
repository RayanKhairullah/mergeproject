<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Kelola Venue Makan</flux:heading>
    </div>

    <div class="flex gap-4 items-end">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari venue..." class="flex-1" />
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
            <flux:heading size="lg">{{ $editingId ? 'Edit Venue' : 'Tambah Venue Baru' }}</flux:heading>
        </div>
        <div class="p-6">
            <form wire:submit="{{ $editingId ? 'update' : 'create' }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-4">
                    <flux:field>
                        <flux:label>Nama Venue</flux:label>
                        <flux:input wire:model="name" placeholder="Masukkan nama venue" />
                        <flux:error name="name" />
                    </flux:field>
                </div>

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? 'Update Venue' : 'Tambah Venue' }}
                    </flux:button>
                    @if($editingId)
                        <flux:button type="button" variant="ghost" wire:click="cancelEdit">
                            Batal
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Venue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Banquet Aktif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($venues as $venue)
                        <tr wire:key="venue-{{ $venue->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $venue->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $venue->active_banquets_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <flux:button size="sm" wire:click="edit({{ $venue->id }})">
                                    Edit
                                </flux:button>
                                <flux:button size="sm" variant="danger" wire:click="delete({{ $venue->id }})" wire:confirm="Yakin ingin menghapus venue ini?">
                                    Hapus
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada venue ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $venues->links() }}
        </div>
    </div>
</div>
