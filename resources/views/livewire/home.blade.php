<!-- Full Screen Hero with Video Background -->
<div class="fixed inset-0 w-full h-screen font-[Roboto]">
    <style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 1s ease-out;
    }
    </style>

    <section class="relative h-full w-full overflow-hidden">
        <!-- Video Background -->
        <div class="absolute inset-0 z-0">
            <video 
                autoplay 
                muted 
                loop 
                playsinline
                class="h-full w-full object-cover"
            >
                <source src="{{ asset('images/opening_old.webm') }}" type="video/webm">
                Your browser does not support the video tag.
            </video>
            <!-- Dark Overlay for better text readability -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black/70"></div>
        </div>

        <!-- Hero Content -->
        <div class="relative z-10 flex h-full items-center justify-center px-4">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Pelindo Logo and Text -->
                <div class="flex flex-col items-center justify-center animate-fade-in">
                    <!-- Pelindo Logo SVG -->
                    <div class="mb-4 group cursor-pointer transition-all duration-750 ease-in-out">
                        <img 
                            src="{{ asset('images/pelindo-teks.svg') }}" 
                            alt="Pelindo" 
                            class="h-24 md:h-32 drop-shadow-2xl group-hover:brightness-110 transition-all duration-750 ease-in-out"
                        >
                    </div>
                    
                    <!-- Branch Text -->
                    <p class="text-lg md:text-xl text-white/80 font-light tracking-wide">
                        Regional 2 Bengkulu
                    </p>
                </div>
            </div>
        </div>

        <!-- Inspirational Quote - Bottom Left -->
        @php
            [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
        @endphp
        <div class="absolute bottom-8 left-8 z-10 max-w-md">
            <blockquote class="space-y-2 text-white">
                <p class="text-lg drop-shadow-lg">&ldquo;{{ trim($message) }}&rdquo;</p>
                <footer class="text-sm text-white/80 drop-shadow-lg">{{ trim($author) }}</footer>
            </blockquote>
        </div>

        <!-- Copyright -->
        <span class="absolute bottom-2 left-1/2 -translate-x-1/2 z-10 text-xs text-white/60">
            All Rights Reserved.
        </span>
    </section>
</div>
