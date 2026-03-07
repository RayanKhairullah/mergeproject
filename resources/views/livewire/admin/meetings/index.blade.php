<div class="space-y-6 px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-zinc-950 dark:text-white">
                Kelola Meeting
            </h1>
            <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 mt-1">
                Jadwal dan manajemen ruang rapat
            </p>
        </div>
        @can('create meetings')
            <flux:modal.trigger name="create-meeting">
                <flux:button variant="primary" icon="plus" class="w-full md:w-auto">
                    Buat Meeting
                </flux:button>
            </flux:modal.trigger>
        @endcan
    </div>

    @can('create meetings')
        <livewire:admin.meetings.create-meeting-modal />
    @endcan

    @foreach($meetings as $meeting)
        @can('update meetings')
            <livewire:admin.meetings.edit-meeting-modal :meetingId="$meeting->id" wire:key="edit-meeting-{{ $meeting->id }}" />
        @endcan
    @endforeach

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-8">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari meeting..." icon="magnifying-glass" />
        
        <flux:select wire:model.live="statusFilter" placeholder="Semua Status">
            <flux:select.option value="">Semua Status</flux:select.option>
            <flux:select.option value="DRAFT">Draft</flux:select.option>
            <flux:select.option value="PENDING_APPROVAL">Pending Approval</flux:select.option>
            <flux:select.option value="PUBLISHED">Published</flux:select.option>
            <flux:select.option value="COMPLETED">Completed</flux:select.option>
            <flux:select.option value="REJECTED">Rejected</flux:select.option>
        </flux:select>
 
        <flux:select wire:model.live="roomFilter" placeholder="Semua Ruang">
            <flux:select.option value="">Semua Ruang</flux:select.option>
            @foreach($rooms as $room)
                <flux:select.option value="{{ $room->id }}">{{ $room->name }}</flux:select.option>
            @endforeach
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ruang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu Mulai</th>
                        <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estimasi Durasi</th>
                        <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Peserta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($meetings as $meeting)
                        <tr wire:key="meeting-{{ $meeting->id }}">
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-mono">
                                #{{ $meeting->id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $meeting->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $meeting->room->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $meeting->started_at?->format('d M Y H:i') }}
                            </td>
                            <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $meeting->duration }} menit
                            </td>
                            <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $meeting->estimated_participants }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge variant="{{ $meeting->status->color() }}">
                                    {{ $meeting->status->label() }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" icon="eye" wire:click="showDetail({{ $meeting->id }})">
                                        <span class="hidden md:inline">Detail</span>
                                    </flux:button>
                                    @can('update meetings')
                                        @if(in_array($meeting->status->value, ['DRAFT', 'PENDING_APPROVAL']))
                                            <flux:modal.trigger name="edit-meeting-{{ $meeting->id }}">
                                                <flux:button size="sm" variant="ghost" icon="pencil-square">
                                                    <span class="hidden md:inline">Edit</span>
                                                </flux:button>
                                            </flux:modal.trigger>
                                        @endif
                                    @endcan
                                    @if($meeting->status->value === 'PENDING_APPROVAL' && auth()->user()->can('approve meetings'))
                                        <flux:button size="sm" variant="primary" icon="check" wire:click="approve({{ $meeting->id }})">
                                            <span class="hidden md:inline">Setujui</span>
                                        </flux:button>
                                    @endif
                                    @can('delete meetings')
                                        @if(in_array($meeting->status->value, ['DRAFT', 'PENDING_APPROVAL']) || auth()->user()->can('approve meetings'))
                                            <flux:button size="sm" variant="danger" icon="trash" wire:click="delete({{ $meeting->id }})" wire:confirm="Yakin ingin menghapus meeting ini?">
                                                <span class="hidden md:inline">Hapus</span>
                                            </flux:button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada meeting ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $meetings->links() }}
        </div>
    </div>

    @if($detailMeeting)
        <flux:modal wire:model="detailId" class="w-full max-w-2xl">
            <div class="space-y-4">
                <flux:heading size="lg">Detail Meeting</flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Judul</p>
                        <p class="font-medium">{{ $detailMeeting->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Ruang</p>
                        <p class="font-medium">{{ $detailMeeting->room->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Kapasitas Ruang</p>
                        <p class="font-medium">{{ $detailMeeting->room->capacity }} orang</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Estimasi Peserta</p>
                        <p class="font-medium">{{ $detailMeeting->estimated_participants }} orang</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Waktu Mulai</p>
                        <p class="font-medium">{{ $detailMeeting->started_at?->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Waktu Selesai</p>
                        <p class="font-medium">{{ $detailMeeting->ended_at?->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Durasi</p>
                        <p class="font-medium">{{ $detailMeeting->duration }} menit</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <flux:badge variant="{{ $detailMeeting->status->color() }}">
                            {{ $detailMeeting->status->label() }}
                        </flux:badge>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Dibuat Oleh</p>
                        <p class="font-medium">{{ $detailMeeting->creator->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tampilkan di Monitor</p>
                        <p class="font-medium">{{ $detailMeeting->show_notes_on_monitor ? 'Ya' : 'Tidak' }}</p>
                    </div>
                </div>

                @if($detailMeeting->notes)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Catatan</p>
                        <div class="text-sm prose prose-sm max-w-none">
                            {!! $detailMeeting->notes !!}
                        </div>
                    </div>
                @endif

                @if($detailMeeting->approver)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Disetujui Oleh</p>
                            <p class="font-medium">{{ $detailMeeting->approver->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Tanggal Persetujuan</p>
                            <p class="font-medium">{{ $detailMeeting->approved_at?->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if($detailMeeting->rejection_reason)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Alasan Penolakan</p>
                        <p class="text-sm text-red-600">{{ $detailMeeting->rejection_reason }}</p>
                    </div>
                @endif

                <div class="flex justify-end">
                    <flux:button wire:click="closeDetail">Tutup</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
