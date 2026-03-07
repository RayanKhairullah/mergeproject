<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<flux:header 
    sticky 
    container 
    :class="request()->routeIs('home') ? 'bg-transparent border-transparent z-50 py-4 md:py-6 transition-all duration-300' : 'bg-white/80 dark:bg-zinc-800/80 backdrop-blur-xl border-b border-zinc-200/50 dark:border-zinc-700/50 z-50 py-3 md:py-4 transition-all duration-300'"
>
    <flux:sidebar.toggle class="lg:hidden {{ request()->routeIs('home') ? 'text-zinc-200 hover:text-white' : '' }}" icon="bars-2" inset="left"/>

    <div class="flex w-full items-center justify-between gap-2 lg:gap-4 pl-4 md:pl-0">
        <!-- 1. Left Section: Logo -->
        <div class="flex flex-1 items-center justify-start min-w-[120px] md:min-w-[200px] shrink overflow-hidden pr-2">
            <a href="{{ route('home') }}" class="flex items-center gap-1.5 md:gap-3 py-1 hover:opacity-80 transition-opacity">
                @if(request()->routeIs('home'))
                    <img src="{{ asset('images/logo-putih.png') }}" alt="Danantara" class="h-4 sm:h-5 w-auto object-contain shrink-0">
                    <span class="text-white/60 text-[10px] sm:text-xs md:text-lg font-light shrink-0">|</span>
                    <img src="{{ asset('images/pelindo-teks.png') }}" alt="Pelindo" class="h-4 sm:h-5 w-auto object-contain shrink min-w-0">
                @else
                    <!-- Danantara Logo -->
                    <img src="{{ asset('images/logo-primer.png') }}" alt="Danantara" class="h-4 sm:h-5 w-auto object-contain shrink-0 dark:hidden">
                    <img src="{{ asset('images/logo-putih.png') }}" alt="Danantara" class="h-4 sm:h-5 w-auto object-contain shrink-0 hidden dark:block">
                    
                    <span class="text-zinc-400 dark:text-white/30 text-[10px] sm:text-xs md:text-lg font-light shrink-0">|</span>
                    
                    <!-- Pelindo Logo -->
                    <img src="{{ asset('images/kop-surat2.png') }}" alt="Pelindo" class="h-4 sm:h-5 w-auto object-contain shrink min-w-0 dark:hidden">
                    <img src="{{ asset('images/pelindo-teks.png') }}" alt="Pelindo" class="h-4 sm:h-5 w-auto object-contain shrink min-w-0 hidden dark:block">
                @endif
            </a>
        </div>

        <!-- 2. Middle Section: Esthetic Centered Navigation -->
        <div class="hidden lg:flex shrink justify-center overflow-hidden">
            @php
                $navColor = request()->routeIs('home') 
                    ? 'text-zinc-200 hover:text-white' 
                    : 'text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors';
            @endphp
            <flux:navbar class="-mb-px flex-none space-x-0 xl:space-x-1">
                <flux:navbar.item variant="subtle" icon="computer-desktop" href="{{ route('vehicles.monitor') }}" :current="request()->routeIs('vehicles.monitor')" class="text-[10px] xl:text-xs px-2! {{ $navColor }} whitespace-nowrap">
                    {{ __('global.monitor_mobil') }}
                </flux:navbar.item>
                <flux:navbar.item variant="subtle" icon="tv" href="{{ route('meetings.monitor') }}" :current="request()->routeIs('meetings.monitor')" class="text-[10px] xl:text-xs px-2! {{ $navColor }} whitespace-nowrap">
                    {{ __('global.monitor_rapat') }}
                </flux:navbar.item>
                <flux:navbar.item variant="subtle" icon="book-open" href="{{ route('books.index') }}" :current="request()->routeIs('books.*')" class="text-[10px] xl:text-xs px-2! {{ $navColor }} whitespace-nowrap">
                    {{ __('global.digital_library') }}
                </flux:navbar.item>

                {{-- Peminjaman Dropdown (Loan + Return) --}}
                <flux:dropdown>
                    <flux:navbar.item
                        variant="subtle"
                        icon="truck"
                        icon-trailing="chevron-down"
                        :current="request()->routeIs(['vehicles.loan', 'vehicles.return'])"
                        class="text-[10px] xl:text-xs px-2! {{ $navColor }} whitespace-nowrap"
                    >
                        {{ __('global.peminjaman') }}
                    </flux:navbar.item>
                    <flux:menu>
                        <flux:menu.item icon="identification" href="{{ route('vehicles.loan') }}">
                            {{ __('global.peminjaman') }}
                        </flux:menu.item>
                        <flux:menu.item icon="key" href="{{ route('vehicles.return') }}">
                            {{ __('global.pengembalian') }}
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

                <flux:navbar.item variant="subtle" icon="clipboard-document-check" href="{{ route('vehicles.inspection') }}" :current="request()->routeIs('vehicles.inspection')" class="text-[10px] xl:text-xs px-2! {{ $navColor }} whitespace-nowrap">
                    {{ __('global.kesiapan_mobil') }}
                </flux:navbar.item>
                <flux:navbar.item variant="subtle" icon="banknotes" href="{{ route('vehicles.expense') }}" :current="request()->routeIs('vehicles.expense')" class="text-[10px] xl:text-xs px-2! {{ $navColor }} whitespace-nowrap">
                    {{ __('global.rupa_rupa') }}
                </flux:navbar.item>
            </flux:navbar>
        </div>

        <!-- 3. Right Section: Utilities & Auth -->
        <div class="flex flex-1 items-center justify-end gap-3 md:gap-4 shrink-0 px-2 lg:px-0">
            <!-- Settings Dropdown (Theme & Language) -->
            <div class="shrink-0">
                <livewire:language-switcher />
            </div>

            @if (Route::has('login'))
                <nav class="flex items-center gap-2 shrink-0">
                    @guest
                        <flux:button 
                            href="{{ route('login') }}" 
                            variant="primary" 
                            size="sm" 
                            class="{{ request()->routeIs('home') ? 'bg-white! text-black! hover:bg-zinc-200!' : 'bg-blue-600 text-white hover:bg-blue-700' }} border-0 rounded-full px-3 lg:px-5 h-8 lg:h-9 text-[10px] lg:text-xs font-semibold whitespace-nowrap"
                        >
                            {{ __('global.log_in') }}
                        </flux:button>
                    @endguest
                </nav>
            @endif
        </div>
    </div>
    {{--            <flux:navbar class="mr-1.5 space-x-0.5 py-0!">--}}
    {{--                <flux:tooltip content="Search" position="bottom">--}}
    {{--                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" label="Search" />--}}
    {{--                </flux:tooltip>--}}
    {{--                <flux:tooltip content="Repository" position="bottom">--}}
    {{--                    <flux:navbar.item--}}
    {{--                        class="h-10 max-lg:hidden [&>div>svg]:size-5"--}}
    {{--                        icon="folder-git-2"--}}
    {{--                        href="https://github.com/laravel/livewire-starter-kit"--}}
    {{--                        target="_blank"--}}
    {{--                        label="Repository"--}}
    {{--                    />--}}
    {{--                </flux:tooltip>--}}
    {{--                <flux:tooltip content="Documentation" position="bottom">--}}
    {{--                    <flux:navbar.item--}}
    {{--                        class="h-10 max-lg:hidden [&>div>svg]:size-5"--}}
    {{--                        icon="book-open-text"--}}
    {{--                        href="https://laravel.com/docs/starter-kits"--}}
    {{--                        target="_blank"--}}
    {{--                        label="Documentation"--}}
    {{--                    />--}}
    {{--                </flux:tooltip>--}}
    {{--            </flux:navbar>--}}

    @auth

        <!-- Desktop User Menu -->
        <flux:dropdown position="top" align="end">
            <flux:profile
                class="cursor-pointer"
                :initials="auth()->user()?->initials() ?? '?'"
            />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()?->initials() ?? '?' }}
                                    </span>
                                </span>

                            {{--                                <div class="grid flex-1 text-left text-sm leading-tight">--}}
                            {{--                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>--}}
                            {{--                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>--}}
                            {{--                                </div>--}}
                        </div>
                    </div>
                </flux:menu.radio.group>

                @can('access dashboard')
                    <flux:menu.separator/>
                    <flux:menu.radio.group>
                        <flux:menu.item href="{{ route('admin.index') }}" icon="shield">
                            {{ __('global.admin_dashboard') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>
                @endcan

                <flux:menu.separator/>

                <!-- @if (config('teams.enabled'))
                    <flux:menu.radio.group>
                        <flux:menu.item href="{{ route('teams.index') }}" icon="users">
                            {{ __('teams.title') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator/>
                @endif -->

                <flux:menu.radio.group>
                    <flux:menu.item href="/settings/profile" icon="cog">
                        {{ __('settings.title') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator/>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('global.log_out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    @endauth
</flux:header>

<!-- Mobile Menu -->
<flux:sidebar stashable sticky class="lg:hidden border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <flux:sidebar.toggle class="lg:hidden"/>
    <flux:navlist variant="outline">
        <flux:navlist.group heading="{{ __('vehicles.title') }}">
            <flux:navlist.item icon="computer-desktop" href="{{ route('vehicles.monitor') }}" :current="request()->routeIs('vehicles.monitor')">
                {{ __('global.monitor_mobil') }}
            </flux:navlist.item>
            <flux:navlist.item icon="clipboard-document-check" href="{{ route('vehicles.inspection') }}" :current="request()->routeIs('vehicles.inspection')">
                {{ __('global.kesiapan_mobil') }}
            </flux:navlist.item>
            <flux:navlist.item icon="banknotes" href="{{ route('vehicles.expense') }}" :current="request()->routeIs('vehicles.expense')">
                {{ __('global.rupa_rupa') }}
            </flux:navlist.item>
        </flux:navlist.group>
        <flux:navlist.group heading="{{ __('global.peminjaman') }}">
            <flux:navlist.item icon="identification" href="{{ route('vehicles.loan') }}" :current="request()->routeIs('vehicles.loan')">
                {{ __('global.peminjaman') }}
            </flux:navlist.item>
            <flux:navlist.item icon="key" href="{{ route('vehicles.return') }}" :current="request()->routeIs('vehicles.return')">
                {{ __('global.pengembalian') }}
            </flux:navlist.item>
        </flux:navlist.group>
        <flux:navlist.group heading="{{ __('meetings.title') }}">
            <flux:navlist.item icon="tv" href="{{ route('meetings.monitor') }}" :current="request()->routeIs('meetings.monitor')">
                {{ __('global.monitor_rapat') }}
            </flux:navlist.item>
        </flux:navlist.group>
        <flux:navlist.group heading="{{ __('global.digital_library') }}">
            <flux:navlist.item icon="book-open" href="{{ route('books.index') }}" :current="request()->routeIs('books.*')">
                {{ __('global.digital_library') }}
            </flux:navlist.item>
        </flux:navlist.group>
    </flux:navlist>

    <flux:spacer/>

</flux:sidebar>

@if(request()->routeIs('home'))
    {{ $slot }}
@else
    <main class="px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>
@endif


@unless(request()->routeIs('home'))
    @include('partials.footer')
@endunless

@fluxScripts
</body>
</html>
