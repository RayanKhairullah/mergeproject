<div class="min-h-screen bg-white dark:bg-zinc-800 font-roboto overflow-y-auto animate-in fade-in duration-700" 
     x-data="{ 
        fullscreen: false,
        currentTime: '',
        toggleFullscreen() {
            if (!document.fullscreenElement) {
                this.$refs.monitor.requestFullscreen().catch(err => {
                    alert(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`);
                });
                this.fullscreen = true;
            } else {
                document.exitFullscreen();
                this.fullscreen = false;
            }
        },
        updateTime() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
     }"
     x-init="updateTime(); setInterval(() => updateTime(), 1000)"
     x-ref="monitor"
     wire:poll.10s>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {{-- Top Bar / Dashboard Header --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 md:gap-8 mb-8 md:mb-12">
            <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 text-center sm:text-left">
                <div class="p-2 sm:p-4">
                    <img src="{{ asset('images/logo-primer.png') }}" alt="Danantara" class="h-6 sm:h-8 w-auto object-contain dark:hidden">
                    <img src="{{ asset('images/logo-putih.png') }}" alt="Danantara" class="h-6 sm:h-8 w-auto object-contain hidden dark:block">
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-black tracking-tight text-zinc-900 dark:text-white uppercase">
                        {{ explode(' ', __('global.monitor_rapat'))[0] }} <span class="text-zinc-500 dark:text-zinc-400 font-light">{{ explode(' ', __('global.monitor_rapat'))[1] ?? '' }}</span>
                    </h1>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-8 w-full md:w-auto">
                {{-- Digital Clock --}}
                <div class="text-center sm:text-right">
                    <div class="text-3xl sm:text-4xl md:text-5xl font-mono font-black tracking-tighter text-zinc-900 dark:text-white" x-text="currentTime"></div>
                    <div class="text-[10px] sm:text-xs uppercase tracking-[0.2em] sm:tracking-[0.3em] font-bold text-zinc-400">{{ now()->translatedFormat('l, d F Y') }}</div>
                </div>

                {{-- Filters & Controls --}}
                <div class="flex items-center gap-3 w-full sm:w-auto justify-center sm:justify-start">
                    <div class="w-full sm:w-48">
                        <flux:select wire:model.live="roomFilter" class="bg-white/50 dark:bg-zinc-900/50 border-0!">
                            <flux:select.option value="">{{ __('global.all_rooms') }}</flux:select.option>
                            @foreach($rooms as $room)
                                <flux:select.option value="{{ $room->id }}">{{ $room->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <flux:button icon="arrows-pointing-out" variant="ghost" @click="toggleFullscreen" class="rounded-xl! h-11 w-11 shadow-sm hidden sm:flex" />
                </div>
            </div>
        </div>

        {{-- Main Content Section --}}
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 md:gap-10">
            
            {{-- LIVE SECTION (Left/Main) --}}
            <div class="xl:col-span-8 space-y-8 md:space-y-10">
                @if($currentMeeting)
                    <div class="relative overflow-hidden bg-white dark:bg-zinc-900 rounded-[2rem] sm:rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6 sm:p-10 lg:p-16 transition-all duration-500">
                        {{-- Background Accent --}}
                        <div class="absolute top-0 right-0 w-64 sm:w-96 h-64 sm:h-96 bg-emerald-500/5 blur-[80px] sm:blur-[120px] rounded-full -mr-10 sm:-mr-20 -mt-10 sm:-mt-20"></div>
                        
                        <div class="relative z-10">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 sm:px-4 sm:py-2 rounded-full bg-emerald-500 text-white text-[10px] sm:text-xs font-black uppercase tracking-widest mb-6 sm:mb-10 shadow-lg shadow-emerald-500/20 animate-pulse">
                                <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-white"></div>
                                {{ __('global.live_meeting') }}
                            </div>

                            <h2 class="text-3xl sm:text-5xl lg:text-7xl font-black text-zinc-900 dark:text-white mb-6 sm:mb-8 leading-tight sm:leading-[1.1] tracking-tighter">
                                {{ $currentMeeting->title }}
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-12 mb-8 sm:mb-12">
                                <div class="space-y-4 sm:space-y-6">
                                    <div class="flex items-center gap-4 sm:gap-6">
                                        <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                                            <flux:icon.building-office class="w-6 h-6 sm:w-8 sm:h-8 text-zinc-400" />
                                        </div>
                                        <div>
                                            <p class="text-[9px] sm:text-[10px] uppercase tracking-widest text-zinc-500 dark:text-zinc-400 font-black mb-1">{{ __('global.conference_room') }}</p>
                                            <p class="text-lg sm:text-2xl font-bold text-zinc-900 dark:text-zinc-100 leading-tight">{{ $currentMeeting->room->name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4 sm:gap-6">
                                        <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                                            <flux:icon.users class="w-6 h-6 sm:w-8 sm:h-8 text-zinc-400" />
                                        </div>
                                        <div>
                                            <p class="text-[9px] sm:text-[10px] uppercase tracking-widest text-zinc-500 dark:text-zinc-400 font-black mb-1">{{ __('global.organizer') }}</p>
                                            <p class="text-lg sm:text-2xl font-bold text-zinc-900 dark:text-zinc-100 leading-tight">{{ $currentMeeting->creator->name }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl sm:rounded-[2rem] p-6 sm:p-8 border border-zinc-100 dark:border-zinc-800 flex flex-col justify-center text-center">
                                    <p class="text-[9px] sm:text-[10px] uppercase tracking-widest text-zinc-400 font-black mb-3 sm:mb-4">{{ __('global.time_window') }}</p>
                                    <div class="flex flex-row items-center justify-center gap-2 sm:gap-3 text-3xl lg:text-4xl xl:text-5xl font-mono font-black text-zinc-900 dark:text-white mb-2 whitespace-nowrap">
                                        <span>{{ $currentMeeting->started_at->format('H:i') }}</span>
                                        <span class="text-zinc-300 dark:text-zinc-600 font-light">/</span>
                                        <span>{{ $currentMeeting->ended_at->format('H:i') }}</span>
                                    </div>
                                    <p class="text-xs sm:text-sm font-bold text-zinc-500">{{ __('global.minutes_total', ['minutes' => $currentMeeting->duration]) }}</p>
                                </div>
                            </div>
                            
                            @if($currentMeeting->show_notes_on_monitor && $currentMeeting->notes)
                                <div class="p-6 sm:p-8 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl sm:rounded-3xl border border-zinc-100 dark:border-zinc-800">
                                    <p class="text-[9px] sm:text-[10px] uppercase tracking-widest text-zinc-400 font-black mb-3 sm:mb-4">{{ __('global.meeting_memo') }}</p>
                                    <div class="text-base sm:text-xl font-medium text-zinc-600 dark:text-zinc-300 leading-relaxed italic">
                                        "{!! nl2br(e(strip_tags(str_replace(['<br>', '<br/>', '</p>', '</li>'], " \n", $currentMeeting->notes)))) !!}"
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="h-full min-h-[400px] sm:min-h-[500px] border-4 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[2rem] sm:rounded-[3rem] flex flex-col items-center justify-center text-center p-8 sm:p-12">
                        <div class="mb-6 sm:mb-8 p-6 sm:p-10 bg-zinc-50 dark:bg-zinc-800/50 rounded-full">
                            <flux:icon.calendar class="w-12 h-12 sm:w-20 sm:h-20 text-zinc-300 dark:text-zinc-700" />
                        </div>
                        <h3 class="text-xl sm:text-3xl font-black text-zinc-400 dark:text-zinc-600 mb-2 uppercase tracking-tighter">{{ __('global.room_available') }}</h3>
                        <p class="text-xs sm:text-sm text-zinc-400 dark:text-zinc-500 font-medium">{{ __('global.no_active_meetings', ['context' => $roomFilter ? 'this room' : 'any conference space']) }}</p>
                    </div>
                @endif
            </div>

            {{-- UPCOMING SECTION (Right/Sidebar) --}}
            <div class="xl:col-span-4 space-y-6 md:space-y-8">
                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-8 shadow-xl dark:shadow-2xl h-full flex flex-col border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white">
                    <div class="flex items-center justify-between mb-6 sm:mb-8">
                        <h4 class="text-base sm:text-lg font-black uppercase tracking-widest text-zinc-900 dark:text-white">{{ __('global.schedule') }}</h4>
                        <span class="px-3 py-1 bg-zinc-200 dark:bg-zinc-800 rounded-full text-[9px] sm:text-[10px] font-bold text-zinc-500 dark:text-zinc-400">{{ __('global.next_24h') }}</span>
                    </div>

                    <div class="space-y-3 sm:space-y-4 flex-1 overflow-y-auto">
                        @forelse($upcomingMeetings as $meeting)
                            <div wire:key="upcoming-{{ $meeting->id }}" class="group p-4 sm:p-6 bg-white dark:bg-zinc-800/30 rounded-2xl sm:rounded-3xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-600 transition-all duration-300 shadow-sm dark:shadow-none">
                                <div class="flex justify-between items-start mb-3 sm:mb-4">
                                    <div class="text-xl sm:text-2xl font-mono font-black text-zinc-900 dark:text-white group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition-colors">
                                        {{ $meeting->started_at->format('H:i') }}
                                    </div>
                                    <span class="text-[9px] sm:text-[10px] font-bold text-zinc-500 uppercase tracking-widest">{{ $meeting->duration }} MIN</span>
                                </div>
                                <h5 class="text-sm sm:text-lg font-bold text-zinc-800 dark:text-zinc-100 mb-2 sm:mb-1 truncate">{{ $meeting->title }}</h5>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-2 sm:mb-3 truncate" title="{{ strip_tags(str_replace(['<br>', '</p>', '</li>'], ' ', $meeting->notes)) }}">
                                    {{ empty(strip_tags($meeting->notes)) ? '-' : str(strip_tags(str_replace(['<br>', '</p>', '</li>'], ' ', $meeting->notes)))->limit(40) }}
                                </div>
                                <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                                    <div class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-[9px] sm:text-[10px] font-black text-zinc-500 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                        {{ $meeting->room->name }}
                                    </div>
                                    <div class="text-[9px] sm:text-[10px] font-bold text-zinc-500 uppercase truncate">{{ $meeting->creator->name }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-10 sm:py-20 opacity-30">
                                <flux:icon.calendar class="w-10 h-10 sm:w-12 sm:h-12 mb-4" />
                                <p class="text-[10px] font-bold uppercase tracking-widest">{{ __('global.no_scheduled_events') }}</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-zinc-200 dark:border-zinc-800 flex justify-between items-center text-[9px] sm:text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">
                        <span>{{ __('global.status_online') }}</span>
                        <div class="flex gap-1">
                            <div class="w-1 h-1 rounded-full bg-emerald-500 opacity-50"></div>
                            <div class="w-1 h-1 rounded-full bg-emerald-500 opacity-80"></div>
                            <div class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


