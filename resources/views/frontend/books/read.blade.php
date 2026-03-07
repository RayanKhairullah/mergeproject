<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $book->title }} - Digital Library</title>
    
    <!-- Tailwind CSS (compiled or CDN for this standalone page) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- DFlip CSS -->
    <link href="https://cdn.jsdelivr.net/npm/dflip/css/dflip.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/dflip/css/themify-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #111;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        ._df_book {
            height: 100vh !important;
            width: 100vw !important;
        }
    </style>
</head>
<body>
    <div class="w-full min-h-screen bg-[#111] flex flex-col relative">
        <!-- Header Overlay -->
        <div class="absolute top-0 left-0 w-full p-4 flex items-center justify-between z-10 pointer-events-none">
            <a href="{{ route('books.show', $book) }}" class="pointer-events-auto flex items-center gap-2 px-4 py-2 bg-black/50 hover:bg-black/80 backdrop-blur-md rounded-full text-white/90 hover:text-white transition-all text-sm font-medium border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 bg-black/50 backdrop-blur-md border border-white/10 rounded-full text-white/80 text-sm font-medium shadow-lg">
                    {{ $book->title }}
                </span>
            </div>
        </div>

        <!-- DFlip Viewer Container -->
        <div class="flex-1 w-full h-full flex items-center justify-center relative overflow-hidden" style="min-height: 100vh;">
            <div class="_df_book" id="pdf-book" 
                 webgl="true" 
                 backgroundcolor="transparent" 
                 source="{{ route('books.stream', $book) }}">
            </div>
        </div>
    </div>

    <!-- jQuery & DFlip JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dflip/js/dflip.min.js"></script>
</body>
</html>
