<div class="space-y-8 font-roboto">
    <!-- Admin Welcome Section (Glassmorphism inspired) -->
    <div class="relative overflow-hidden rounded-3xl bg-zinc-900 dark:bg-black p-8 sm:p-12 shadow-2xl">
        {{-- Decorative background elements --}}
        <div class="pointer-events-none absolute -top-24 -right-24 w-96 h-96 bg-teal-500/10 blur-[120px] rounded-full"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-24 w-96 h-96 bg-blue-500/10 blur-[120px] rounded-full"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-center md:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-500/20 text-teal-400 text-xs font-black uppercase tracking-widest mb-4">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                    </span>
                    {{ __('dashboard.system_status') }}: {{ __('dashboard.online') }}
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tighter text-white mb-4">
                    {{ __('dashboard.title') }}
                </h1>
                <p class="text-zinc-400 text-lg font-light max-w-xl">
                    {{ __('dashboard.subtitle') }}
                </p>
            </div>
            
            <div class="flex items-center gap-4 bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-2xl shadow-inner">
                <div class="w-12 h-12 rounded-full bg-teal-500/20 flex items-center justify-center">
                    <flux:icon.lock-closed class="w-6 h-6 text-teal-400" />
                </div>
                <div>
                    <p class="text-[10px] uppercase font-black text-zinc-500 tracking-tighter">{{ __('dashboard.session_scale') }}</p>
                    <p class="text-white font-bold text-lg">{{ __('dashboard.secure_encrypted') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Tools Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total Users Card --}}
        <div class="group bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 hover:border-teal-500 transition-all duration-300 shadow-sm overflow-hidden relative">
             <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                <flux:icon.users class="w-32 h-32 text-zinc-900 dark:text-white" />
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-6">
                    <flux:icon.users class="w-6 h-6" />
                </div>
                <h3 class="text-zinc-500 text-sm font-bold uppercase tracking-widest mb-1">{{ __('users.title') }}</h3>
                <p class="text-3xl font-black text-zinc-900 dark:text-white mb-6">{{ __('dashboard.user_config') }}</p>
                <flux:button href="{{ route('admin.users.index') }}" variant="ghost" icon-trailing="chevron-right" size="sm" class="text-teal-600 dark:text-teal-400 -ml-3">
                    {{ __('dashboard.open_management') }}
                </flux:button>
            </div>
        </div>

        {{-- Roles & Permissions --}}
        <div class="group bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 hover:border-blue-500 transition-all duration-300 shadow-sm overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                <flux:icon.shield-check class="w-32 h-32 text-zinc-900 dark:text-white" />
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-6">
                    <flux:icon.shield-check class="w-6 h-6" />
                </div>
                <h3 class="text-zinc-500 text-sm font-bold uppercase tracking-widest mb-1">{{ __('roles.title') }}</h3>
                <p class="text-3xl font-black text-zinc-900 dark:text-white mb-6">{{ __('dashboard.roles_permissions') }}</p>
                <flux:button href="{{ route('admin.roles.index') }}" variant="ghost" icon-trailing="chevron-right" size="sm" class="text-blue-600 dark:text-blue-400 -ml-3">
                    {{ __('dashboard.adjust_policy') }}
                </flux:button>
            </div>
        </div>

        {{-- System Health / Logs --}}
        <div class="group bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 hover:border-purple-500 transition-all duration-300 shadow-sm overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                <flux:icon.circle-stack class="w-32 h-32 text-zinc-900 dark:text-white" />
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-6">
                    <flux:icon.circle-stack class="w-6 h-6" />
                </div>
                <h3 class="text-zinc-500 text-sm font-bold uppercase tracking-widest mb-1">{{ __('dashboard.infrastructure') }}</h3>
                <p class="text-3xl font-black text-zinc-900 dark:text-white mb-6">{{ __('dashboard.logs_monitoring') }}</p>
                <flux:button variant="ghost" icon-trailing="chevron-right" size="sm" class="text-purple-600 dark:text-purple-400 -ml-3">
                    {{ __('dashboard.view_status') }}
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Recent Logs / Activity (Draft) -->
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 p-8 shadow-sm">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-black text-zinc-900 dark:text-white">{{ __('dashboard.recent_activity') }}</h2>
                <p class="text-zinc-500 text-sm mt-1">{{ __('dashboard.activity_subtitle') }}</p>
            </div>
            <flux:button variant="outline" size="sm" icon="arrow-path">{{ __('dashboard.refresh') }}</flux:button>
        </div>

        <div class="space-y-4">
            <div class="flex items-center gap-4 p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800">
                <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <flux:icon.check-circle class="w-5 h-5" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-zinc-900 dark:text-zinc-200">{{ __('dashboard.db_connection_success') }}</p>
                    <p class="text-xs text-zinc-500">10 seconds ago • System Node A</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold uppercase">{{ __('dashboard.success') }}</span>
                </div>
            </div>

            <div class="flex items-center gap-4 p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800">
                <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-500">
                    <flux:icon.user-plus class="w-5 h-5" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-zinc-900 dark:text-zinc-200">{{ __('dashboard.new_moderator') }}</p>
                    <p class="text-xs text-zinc-500">2 hours ago • Authentication Module</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-bold uppercase">{{ __('dashboard.auth') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
