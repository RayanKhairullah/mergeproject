<div class="space-y-6 px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-zinc-950 dark:text-white">
                Kelola Banquet
            </h1>
            <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 mt-1">
                Manajemen jamuan dan katering
            </p>
        </div>
        @can('create banquets')
            <flux:modal.trigger name="create-banquet">
                <flux:button variant="primary" icon="plus" class="w-full md:w-auto">
                    Buat Banquet
                </flux:button>
            </flux:modal.trigger>
        @endcan
    </div>

    @can('create banquets')
        <livewire:admin.banquets.create-banquet-modal />
    @endcan

    @foreach($banquets as $banquet)
        @can('update banquets')
            <livewire:admin.banquets.edit-banquet-modal :banquetId="$banquet->id" wire:key="edit-banquet-{{ $banquet->id }}" />
        @endcan
    @endforeach

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-8">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari banquet..." icon="magnifying-glass" />
        
        <flux:select wire:model.live="statusFilter" placeholder="Semua Status">
            <flux:select.option value="">Semua Status</flux:select.option>
            <flux:select.option value="DRAFT">Draft</flux:select.option>
            <flux:select.option value="PENDING_APPROVAL">Pending Approval</flux:select.option>
            <flux:select.option value="PUBLISHED">Published</flux:select.option>
            <flux:select.option value="COMPLETED">Completed</flux:select.option>
            <flux:select.option value="REJECTED">Rejected</flux:select.option>
        </flux:select>
 
        <flux:select wire:model.live="venueFilter" placeholder="Semua Venue">
            <flux:select.option value="">Semua Venue</flux:select.option>
            @foreach($venues as $venue)
                <flux:select.option value="{{ $venue->id }}">{{ $venue->name }}</flux:select.option>
            @endforeach
        </flux:select>
 
        <flux:select wire:model.live="guestTypeFilter" placeholder="Semua Tipe Tamu">
            <flux:select.option value="">Semua Tipe Tamu</flux:select.option>
            <flux:select.option value="VVIP">VVIP</flux:select.option>
            <flux:select.option value="VIP">VIP</flux:select.option>
            <flux:select.option value="Internal">Internal</flux:select.option>
        </flux:select>
 
        <flux:input type="date" wire:model.live="dateFilter" />
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
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul</th>
                        <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipe Tamu</th>
                        <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Venue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tamu</th>
                        <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($banquets as $banquet)
                        <tr wire:key="banquet-{{ $banquet->id }}">
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-mono">
                                #{{ $banquet->id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $banquet->title }}
                            </td>
                            <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $banquet->guest_type->value }}
                            </td>
                            <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $banquet->venue->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $banquet->scheduled_at?->format('d M Y H:i') }}
                            </td>
                            <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $banquet->estimated_guests }}
                            </td>
                            <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $banquet->cost ? 'Rp ' . number_format($banquet->cost, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge variant="{{ $banquet->status->color() }}">
                                    {{ $banquet->status->label() }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" icon="eye" wire:click="showDetail({{ $banquet->id }})">
                                        <span class="hidden md:inline">Detail</span>
                                    </flux:button>
                                    @can('update banquets')
                                        @if(in_array($banquet->status->value, ['DRAFT', 'PENDING_APPROVAL']))
                                            <flux:modal.trigger name="edit-banquet-{{ $banquet->id }}">
                                                <flux:button size="sm" variant="ghost" icon="pencil-square">
                                                    <span class="hidden md:inline">Edit</span>
                                                </flux:button>
                                            </flux:modal.trigger>
                                        @endif
                                    @endcan
                                    @if($banquet->status->value === 'PENDING_APPROVAL' && auth()->user()->can('approve banquets'))
                                        <flux:button size="sm" variant="primary" icon="check" wire:click="approve({{ $banquet->id }})">
                                            <span class="hidden md:inline">Setujui</span>
                                        </flux:button>
                                    @endif
                                    @can('delete banquets')
                                        @if(in_array($banquet->status->value, ['DRAFT', 'PENDING_APPROVAL']) || auth()->user()->can('approve banquets'))
                                            <flux:button size="sm" variant="danger" icon="trash" wire:click="delete({{ $banquet->id }})" wire:confirm="Yakin ingin menghapus banquet ini?">
                                                <span class="hidden md:inline">Hapus</span>
                                            </flux:button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada banquet ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $banquets->links() }}
        </div>
    </div>

    @if($detailBanquet)
        <flux:modal wire:model="detailId" class="w-full max-w-2xl">
            <div class="space-y-4">
                <flux:heading size="lg">Detail Banquet</flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Judul</p>
                        <p class="font-medium">{{ $detailBanquet->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tipe Tamu</p>
                        <p class="font-medium">{{ $detailBanquet->guest_type->value }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Venue</p>
                        <p class="font-medium">{{ $detailBanquet->venue->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Estimasi Tamu</p>
                        <p class="font-medium">{{ $detailBanquet->estimated_guests }} orang</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Biaya Jamuan</p>
                        <p class="font-medium">{{ $detailBanquet->cost ? 'Rp ' . number_format($detailBanquet->cost, 0, ',', '.') : 'Belum ditentukan' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tanggal</p>
                        <p class="font-medium">{{ $detailBanquet->scheduled_at?->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <flux:badge variant="{{ $detailBanquet->status->color() }}">
                            {{ $detailBanquet->status->label() }}
                        </flux:badge>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Dibuat Oleh</p>
                        <p class="font-medium">{{ $detailBanquet->creator->name }}</p>
                    </div>
                </div>

                @if($detailBanquet->description)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Catatan</p>
                        <p class="text-sm">{{ $detailBanquet->description }}</p>
                    </div>
                @endif

                @if($detailBanquet->approver)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Disetujui Oleh</p>
                            <p class="font-medium">{{ $detailBanquet->approver->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Tanggal Persetujuan</p>
                            <p class="font-medium">{{ $detailBanquet->approved_at?->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if($detailBanquet->rejection_reason)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Alasan Penolakan</p>
                        <p class="text-sm text-red-600">{{ $detailBanquet->rejection_reason }}</p>
                    </div>
                @endif

                <div class="flex justify-end">
                    <flux:button wire:click="closeDetail">Tutup</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
