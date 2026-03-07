<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 lg:dark:bg-zinc-900/50">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>

    <a href="{{ route('home') }}" class="mr-5 flex items-center space-x-2">
        <x-app-logo class="size-8"></x-app-logo>
    </a>

    <flux:navlist variant="outline">
        <flux:navlist.group heading="{{ __('sidebar.platform') }}" class="grid">
            <flux:navlist.item icon="home" :href="route('admin.index')" :current="request()->routeIs('admin.index')">{{ __('sidebar.dashboard') }}</flux:navlist.item>
        </flux:navlist.group>

        @canany(['view users', 'view roles', 'view permissions'])
            <flux:navlist.group heading="{{ __('sidebar.users') }}" class="grid">
                @can('view users')
                    <flux:navlist.item icon="user" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')">
                        {{ __('users.title') }}
                    </flux:navlist.item>
                @endcan
                @can('view roles')
                    <flux:navlist.item icon="shield-user" :href="route('admin.roles.index')" :current="request()->routeIs('admin.roles.*')">
                        {{ __('roles.title') }}
                    </flux:navlist.item>
                @endcan
                @can('view permissions')
                    <flux:navlist.item icon="shield-check" :href="route('admin.permissions.index')" :current="request()->routeIs('admin.permissions.*')">
                        {{ __('permissions.title') }}
                    </flux:navlist.item>
                @endcan
            </flux:navlist.group>
        @endcanany

        {{-- Master Data Section --}}
        <flux:navlist.group expandable heading="{{ __('sidebar.master_data') }}" icon="database" :expanded="request()->routeIs(['admin.vehicles.*', 'admin.rooms.*', 'admin.dining-venues.*', 'admin.categories.*'])" class="grid">
            <flux:navlist.item icon="truck" :href="route('admin.vehicles.index')" :current="request()->routeIs('admin.vehicles.*')">
                {{ __('sidebar.vehicles') }}
            </flux:navlist.item>
            <flux:navlist.item icon="building-office" :href="route('admin.rooms.index')" :current="request()->routeIs('admin.rooms.*')">
                {{ __('sidebar.meeting_rooms') }}
            </flux:navlist.item>
            <flux:navlist.item icon="building-storefront" :href="route('admin.dining-venues.index')" :current="request()->routeIs('admin.dining-venues.*')">
                {{ __('sidebar.dining_venues') }}
            </flux:navlist.item>
            <flux:navlist.item icon="book-open" :href="route('admin.categories.index')" :current="request()->routeIs('admin.categories.*')">
                {{ __('sidebar.book_categories') }}
            </flux:navlist.item>
        </flux:navlist.group>

        {{-- Digital Library Section --}}
        <flux:navlist.group expandable heading="{{ __('sidebar.digital_library') }}" icon="lightbulb" :expanded="request()->routeIs('admin.books.*')" class="grid">
            <flux:navlist.item icon="book-open" :href="route('admin.books.index')" :current="request()->routeIs('admin.books.*')">
                {{ __('sidebar.manage_books') }}
            </flux:navlist.item>
        </flux:navlist.group>

        {{-- Vehicle Management Section --}}
        <flux:navlist.group expandable heading="{{ __('sidebar.vehicle_reports') }}" icon="document-chart-bar" :expanded="request()->routeIs(['admin.loans.*', 'admin.inspections.*', 'admin.expenses.*'])" class="grid">
            <flux:navlist.item icon="truck" :href="route('admin.loans.index')" :current="request()->routeIs('admin.loans.*')">
                {{ __('sidebar.loans') }}
            </flux:navlist.item>
            <flux:navlist.item icon="clipboard-document-check" :href="route('admin.inspections.index')" :current="request()->routeIs('admin.inspections.*')">
                {{ __('sidebar.vehicle_readiness') }}
            </flux:navlist.item>
            <flux:navlist.item icon="currency-dollar" :href="route('admin.expenses.index')" :current="request()->routeIs('admin.expenses.*')">
                {{ __('sidebar.expenses') }}
            </flux:navlist.item>
        </flux:navlist.group>

        {{-- Meeting & Banquet Management Section --}}
        <flux:navlist.group expandable heading="{{ __('sidebar.meeting_banquet') }}" icon="calendar" :expanded="request()->routeIs(['admin.meetings.*', 'admin.banquets.*'])" class="grid">
            <flux:navlist.item icon="calendar" :href="route('admin.meetings.index')" :current="request()->routeIs('admin.meetings.*')">
                {{ __('sidebar.meeting') }}
            </flux:navlist.item>
            <flux:navlist.item icon="cake" :href="route('admin.banquets.index')" :current="request()->routeIs('admin.banquets.*')">
                {{ __('sidebar.banquet') }}
            </flux:navlist.item>
        </flux:navlist.group>
    </flux:navlist>

    <flux:spacer/>



    <div class="border-t border-zinc-200 dark:border-zinc-700 mt-4 pt-4 px-4">
        <livewire:language-switcher mode="full" />
    </div>


    @auth
        <!-- Desktop User Menu -->
        <flux:dropdown position="bottom" align="start">
            <flux:profile
                :name="auth()->user()->name"
                :initials="auth()->user()->initials()"
                icon-trailing="chevrons-up-down"
            />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator/>

                <flux:menu.radio.group>
                    <flux:menu.item href="/settings/profile" icon="cog">{{ __('global.settings') }}</flux:menu.item>
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
</flux:sidebar>

<!-- Mobile User Menu -->
<flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left"/>

    <flux:spacer/>

    @auth
        <flux:dropdown position="top" align="end">
            <flux:profile
                :initials="auth()->user()?->initials() ?? '?'"
                icon-trailing="chevron-down"
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

                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()?->name ?? 'User' }}</span>
                                <span class="truncate text-xs">{{ auth()->user()?->email ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator/>

                <flux:menu.radio.group>
                    <flux:menu.item href="/settings/profile" icon="cog">
                        {{ __('global.settings') }}
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

{{ $slot }} 

@fluxScripts
<x-livewire-alert::scripts />
<x-livewire-alert::flash />

</body>
</html>
