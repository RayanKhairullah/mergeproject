<div class="min-h-screen bg-gradient-to-br from-zinc-50 to-zinc-100 dark:from-zinc-900 dark:to-zinc-800 p-4 md:p-8" wire:poll.10s>
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <flux:heading size="xl" class="tracking-tighter text-balance">Monitor Rapat</flux:heading>
            
            <div class="w-64">
                <flux:select wire:model.live="roomFilter" placeholder="Semua Ruang">
                    <flux:select.option value="">Semua Ruang</flux:select.option>
                    @foreach($rooms as $room)
                        <flux:select.option value="{{ $room->id }}">{{ $room->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        @if($currentMeeting)
            <div class="bg-white/90 dark:bg-zinc-800/90 backdrop-blur-md rounded-2xl shadow-xl p-6 md:p-8 border-2 border-mint-500 dark:border-mint-600">
                <div class="flex items-center justify-between mb-6">
                    <flux:badge variant="primary" size="lg" class="bg-mint-600 text-white">
                        MEETING BERLANGSUNG
                    </flux:badge>
                    <div class="text-right text-gray-600 dark:text-gray-400">
                        <div class="text-sm font-mono">{{ $currentMeeting->started_at->format('H:i') }} - {{ $currentMeeting->ended_at->format('H:i') }}</div>
                        <div class="text-xs">{{ $currentMeeting->duration }} menit</div>
                    </div>
                </div>
                
                <flux:heading size="2xl" class="mb-4 tracking-tighter text-balance text-gray-950 dark:text-white">
                    {{ $currentMeeting->title }}
                </flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <flux:text class="text-sm text-gray-600 dark:text-gray-400">Ruang</flux:text>
                        <flux:text class="font-medium text-lg">{{ $currentMeeting->room->name }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm text-gray-600 dark:text-gray-400">Penyelenggara</flux:text>
                        <flux:text class="font-medium text-lg">{{ $currentMeeting->creator->name }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm text-gray-600 dark:text-gray-400">Peserta</flux:text>
                        <flux:text class="font-medium text-lg">{{ $currentMeeting->estimated_participants }} orang</flux:text>
                    </div>
                </div>
                
                @if($currentMeeting->show_notes_on_monitor && $currentMeeting->notes)
                    <div class="bg-gray-50 dark:bg-zinc-900/50 rounded-lg p-4 border border-gray-200 dark:border-zinc-700">
                        <flux:text class="text-sm text-gray-600 dark:text-gray-400 mb-2">Catatan Meeting</flux:text>
                        <div class="prose prose-sm max-w-none dark:prose-invert">
                            {!! nl2br(e($currentMeeting->notes)) !!}
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-xl p-8 text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <flux:heading size="lg" class="text-gray-600 dark:text-gray-400 mb-2">Tidak Ada Meeting Berlangsung</flux:heading>
                <flux:text class="text-gray-500 dark:text-gray-500">{{ $roomFilter ? 'Ruang ini' : 'Semua ruang' }} sedang tersedia</flux:text>
            </div>
        @endif

        @if($upcomingMeetings->count() > 0)
            <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-xl p-6 md:p-8">
                <flux:heading size="lg" class="mb-6 tracking-tight">Meeting Mendatang</flux:heading>
                
                <div class="space-y-4">
                    @foreach($upcomingMeetings as $meeting)
                        <div wire:key="upcoming-{{ $meeting->id }}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-900/50 rounded-lg border border-gray-200 dark:border-zinc-700 hover:border-mint-500 dark:hover:border-mint-600 transition-all duration-300">
                            <div class="flex-1">
                                <flux:text class="font-medium text-lg">{{ $meeting->title }}</flux:text>
                                <div class="flex items-center gap-4 mt-1">
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-mono">{{ $meeting->started_at->format('H:i') }}</span> - {{ $meeting->room->name }}
                                    </flux:text>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $meeting->creator->name }}
                                    </flux:text>
                                </div>
                            </div>
                            <div class="text-right">
                                <flux:text class="font-medium text-mint-600 dark:text-mint-500">{{ $meeting->duration }} menit</flux:text>
                                <flux:text class="text-xs text-gray-500 dark:text-gray-500">{{ $meeting->estimated_participants }} peserta</flux:text>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="text-center text-xs text-gray-500 dark:text-gray-500">
            Terakhir diperbarui: {{ now()->format('H:i:s') }} • Auto-refresh setiap 10 detik
        </div>
    </div>
</div>
