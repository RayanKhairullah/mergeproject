<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['title' => $title ?? null])
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-r dark:border-neutral-800">
                <div class="absolute inset-0 bg-zinc-950/20 z-10"></div>
                <!-- Blur Shadow / Vignette (Bottom Left) -->
                <div class="absolute inset-0 bg-gradient-to-tr from-black/80 via-transparent to-transparent z-15"></div>
                <!-- Background Image -->
                <div class="absolute inset-0 z-0">
                    <img src="{{ asset('images/pelabuhan.webp') }}" alt="Port Background" class="w-full h-full object-cover">
                </div>
                
                <a href="{{ route('home') }}" class="relative z-20 flex items-center gap-3">
                    <div class="flex items-center gap-3 bg-black/20 backdrop-blur-md px-4 py-2 rounded-xl border border-white/10">
                        <img src="{{ asset('images/logo-putih.png') }}" alt="Danantara" class="h-6 w-auto object-contain">
                        <span class="text-white/30 text-xl font-light">|</span>
                        <img src="{{ asset('images/pelindo-teks.png') }}" alt="Pelindo" class="h-6 w-auto object-contain">
                    </div>
                </a>

                @php
                    [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                @endphp

                <div class="relative z-16 mt-auto">
                    <div class="max-w-md p-6">
                        <blockquote class="space-y-4">
                            <p class="text-xl font-light leading-relaxed tracking-wide italic">&ldquo;{{ trim($message) }}&rdquo;</p>
                            <footer class="text-sm font-medium uppercase tracking-[0.2em] text-white/60">&mdash; {{ trim($author) }}</footer>
                        </blockquote>
                    </div>
                </div>
            </div>
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-4 lg:hidden mb-4">
                        <div class="flex items-center gap-3 bg-zinc-100 dark:bg-zinc-800 px-5 py-3 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-700">
                            <img src="{{ asset('images/logo-putih.png') }}" alt="Danantara" class="h-7 w-auto object-contain brightness-0 dark:brightness-100">
                            <span class="text-zinc-300 dark:text-zinc-600 text-xl font-light">|</span>
                            <img src="{{ asset('images/pelindo-teks.png') }}" alt="Pelindo" class="h-7 w-auto object-contain brightness-0 dark:brightness-100">
                        </div>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>
        @livewireScripts
        @fluxScripts
    </body>
</html>
