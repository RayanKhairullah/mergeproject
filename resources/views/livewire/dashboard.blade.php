<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12 font-roboto animate-in fade-in duration-700">
    <!-- Hero Welcome Section (Glassmorphism inspired) -->
    <div class="relative overflow-hidden rounded-3xl bg-zinc-900 dark:bg-black p-8 sm:p-12 shadow-2xl">
        {{-- Decorative background elements --}}
        <div class="pointer-events-none absolute -top-24 -right-24 w-96 h-96 bg-teal-500/20 blur-[120px] rounded-full"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-24 w-96 h-96 bg-blue-500/10 blur-[120px] rounded-full"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-center md:text-left">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tighter text-white mb-4 animate-in fade-in slide-in-from-left duration-700">
                    {{ __('global.welcome') }}, <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-400">{{ auth()->user()?->name }}</span>!
                </h1>
                <p class="text-zinc-400 text-lg sm:text-xl font-light max-w-xl">
                    {{ __('global.dashboard_description') }}
                </p>
            </div>
            <div class="hidden lg:block shrink-0">
                <div class="p-6 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-inner">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-teal-500/20 flex items-center justify-center">
                            <flux:icon.clock class="w-6 h-6 text-teal-400" />
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest font-black text-zinc-500">{{ now()->translatedFormat('d M Y') }}</p>
                            <p class="text-white font-bold text-xl tabular-nums">{{ now()->format('H:i') }} <span class="text-xs font-normal text-zinc-500 ml-1">WIB</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Statistics Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        {{-- Total Vehicles --}}
        <div class="group bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <flux:icon.truck class="w-5 h-5" />
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">{{ __('global.total') }}</span>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tabular-nums">{{ number_format($this->totalVehicles) }}</p>
            <p class="text-xs text-zinc-500 mt-1">{{ __('vehicles.title') }}</p>
        </div>

        {{-- Active Loans --}}
        <div class="group bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 hover:border-emerald-500/50 dark:hover:border-emerald-500/50 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <flux:icon.identification class="w-5 h-5" />
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">{{ __('global.active') }}</span>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tabular-nums">{{ number_format($this->activeLoans) }}</p>
            <p class="text-xs text-zinc-500 mt-1">{{ __('global.peminjaman') }}</p>
        </div>

        {{-- Upcoming Meetings --}}
        <div class="group bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 hover:border-blue-500/50 dark:hover:border-blue-500/50 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <flux:icon.calendar class="w-5 h-5" />
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">{{ __('global.yad') }}</span>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tabular-nums">{{ number_format($this->upcomingMeetings) }}</p>
            <p class="text-xs text-zinc-500 mt-1">{{ __('global.monitor_rapat') }}</p>
        </div>

        {{-- Total Books --}}
        <div class="group bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 hover:border-purple-500/50 dark:hover:border-purple-500/50 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <flux:icon.book-open class="w-5 h-5" />
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">{{ __('global.collection') }}</span>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tabular-nums">{{ number_format($this->totalBooks) }}</p>
            <p class="text-xs text-zinc-500 mt-1">{{ __('global.digital_library') }}</p>
        </div>
    </div>

    <!-- Two-Column Layout -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        <!-- Main Quick Access (2 Cols) -->
        <div class="xl:col-span-2 space-y-6">
            <div class="flex items-center justify-between px-2 mb-6">
                <h2 class="text-xl font-black tracking-tight text-zinc-900 dark:text-white uppercase pb-2 border-b-2 border-zinc-200 dark:border-zinc-800 w-fit">{{ __('global.quick_access') }}</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Manage Meetings --}}
                @can('view meetings')
                <a href="{{ route('admin.meetings.index') }}" class="group relative p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl hover:border-teal-500/50 transition-all duration-300 hover:shadow-xl hover:shadow-teal-500/5 overflow-hidden">
                    <div class="absolute top-0 right-0 p-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-up-right class="w-5 h-5 text-teal-500" />
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <flux:icon.calendar class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="font-bold text-zinc-900 dark:text-white text-lg">{{ __('global.manage_meetings') }}</h3>
                            <p class="text-zinc-500 text-sm leading-relaxed mt-1">Kelola jadwal rapat, ruangan, dan persetujuan meeting.</p>
                        </div>
                    </div>
                </a>
                @endcan

                {{-- Manage Banquets --}}
                @can('view banquets')
                <a href="{{ route('admin.banquets.index') }}" class="group relative p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl hover:border-emerald-500/50 transition-all duration-300 hover:shadow-xl hover:shadow-emerald-500/5 overflow-hidden">
                    <div class="absolute top-0 right-0 p-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon.arrow-up-right class="w-5 h-5 text-emerald-500" />
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <flux:icon.cake class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="font-bold text-zinc-900 dark:text-white text-lg">{{ __('global.manage_banquets') }}</h3>
                            <p class="text-zinc-500 text-sm leading-relaxed mt-1">Kelola jamuan, katering, dan persetujuan banquet.</p>
                        </div>
                    </div>
                </a>
                @endcan
            </div>
        </div>

        <!-- Sidebar (1 Col) -->
        <div class="space-y-10">
            @can('access dashboard')
                <div>
                    <h2 class="text-lg font-black tracking-tight text-zinc-900 dark:text-white uppercase mb-5 px-1 pb-2 border-b-2 border-teal-500 w-fit">{{ __('global.administration') }}</h2>
                    <div class="space-y-4">
                        {{-- Admin Access --}}
                        <a href="{{ route('admin.index') }}" class="group flex items-center p-4 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-2xl shadow-lg hover:shadow-teal-500/30 transition-all duration-300 transform hover:-translate-y-1">
                            <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center mr-4">
                                <flux:icon.lock-open class="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-sm">{{ __('global.admin_dashboard') }}</h3>
                                <p class="text-teal-50 text-[10px] opacity-80">{{ __('global.system_management') }}</p>
                            </div>
                            <flux:icon.chevron-right class="ml-auto w-4 h-4 text-white/50 group-hover:translate-x-1 transition-transform" />
                        </a>

                        {{-- Inspection Form --}}
                        <a href="{{ route('vehicles.inspection') }}" class="group flex items-center p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl hover:border-teal-500/50 transition-all shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center mr-4">
                                <flux:icon.clipboard-document-check class="w-5 h-5 text-teal-600 dark:text-teal-400" />
                            </div>
                            <div>
                                <h3 class="font-bold text-zinc-900 dark:text-white text-sm">{{ __('global.kesiapan_mobil') }}</h3>
                                <p class="text-zinc-500 text-[10px] uppercase tracking-widest font-bold">{{ __('global.security_inspection') }}</p>
                            </div>
                        </a>
                    </div>
                </div>
            @endcan

            {{-- Global Settings --}}
            <div>
                <h2 class="text-lg font-black tracking-tight text-zinc-900 dark:text-white uppercase mb-5 px-1 pb-2 border-b-2 border-zinc-300 dark:border-zinc-700 w-fit">{{ __('global.settings') }}</h2>
                <a href="{{ route('settings.profile') }}" class="flex items-center p-4 bg-zinc-100 dark:bg-zinc-800/50 rounded-2xl hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-colors">
                    <div class="w-11 h-11 rounded-full bg-white dark:bg-zinc-700 flex items-center justify-center mr-4 shadow-sm text-zinc-600 dark:text-zinc-300">
                        <flux:icon.user class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="font-bold text-zinc-900 dark:text-white text-sm">{{ __('global.profile_update') }}</h3>
                        <p class="text-zinc-500 text-xs">{{ __('global.profile_update_desc') }}</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    @once
        <style>
            .hover-lift { transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease; }
            .hover-lift:hover { transform: translateY(-4px); }
        </style>
    @endonce
</div>

