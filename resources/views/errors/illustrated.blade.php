<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ config('app.name') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .error-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #2dd4bf 50%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-grid {
            background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
            background-size: 40px 40px;
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-200 antialiased min-h-screen flex items-center justify-center p-6 bg-grid">
    <div class="relative max-w-2xl w-full text-center">
        {{-- Decorative Glow --}}
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-500/10 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-500/10 blur-[120px] rounded-full pointer-events-none"></div>

        <div class="relative z-10">
            <h1 class="text-[8rem] md:text-[12rem] font-black leading-none tracking-tighter error-gradient select-none">
                @yield('code')
            </h1>
            
            <div class="mt-4 space-y-4">
                <h2 class="text-3xl md:text-4xl font-bold text-white tracking-tight">
                    @yield('message')
                </h2>
                <p class="text-zinc-500 max-w-md mx-auto text-lg leading-relaxed">
                    @yield('description')
                </p>
            </div>

            <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}" class="group relative px-8 py-4 bg-white text-zinc-950 font-bold rounded-2xl hover:bg-emerald-400 transition-all duration-300 shadow-xl shadow-white/5 overflow-hidden">
                    <span class="relative z-10 flex items-center gap-2">
                        <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Safety
                    </span>
                </a>
                
                <button onclick="window.location.reload()" class="px-8 py-4 bg-zinc-900 text-zinc-400 font-bold rounded-2xl border border-zinc-800 hover:text-white hover:border-zinc-700 transition-all duration-300">
                    Try Reloading
                </button>
            </div>

            <div class="mt-20 pt-10 border-t border-zinc-900/50 flex flex-col items-center gap-4">
                <div class="flex items-center gap-6 grayscale opacity-30 hover:grayscale-0 hover:opacity-100 transition-all duration-500">
                    <img src="{{ asset('images/icon_web.svg') }}" class="h-8 w-auto" alt="Logo">
                </div>
                <p class="text-xs text-zinc-600 font-medium tracking-widest uppercase">
                    &copy; {{ date('Y') }} Pelindo Internship • System Error
                </p>
            </div>
        </div>
    </div>
</body>
</html>
