<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $book->title }} - Digital Library</title>
    
    <!-- Tailwind CSS (compiled or CDN for this standalone page) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- DFlip CSS -->
    <link href="https://cdn.jsdelivr.net/npm/dflip/css/dflip.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/dflip/css/themify-icons.min.css" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #111;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            width: 100%;
            height: 100%;
        }
        
        .reader-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #111;
            overflow: hidden;
        }

        ._df_book {
            width: 100% !important;
            height: 100% !important;
            margin: 0 auto !important;
            display: block !important;
        }
        
        /* Force DFlip to center content */
        ._df_book ._df_thumb {
            margin: 0 auto !important;
        }
        
        /* Mobile optimizations */
        @media (max-width: 640px) {
            .book-title-mobile {
                max-width: 180px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <!-- Header Overlay -->
    <div class="fixed top-0 left-0 w-full p-3 sm:p-4 flex items-center justify-between z-50 pointer-events-none">
        <a href="{{ route('books.show', $book) }}" class="pointer-events-auto flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-black/50 hover:bg-black/80 backdrop-blur-md rounded-full text-white/90 hover:text-white transition-all text-xs sm:text-sm font-medium border border-white/10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            <span class="hidden sm:inline">Kembali</span>
        </a>
        <div class="flex items-center gap-2 sm:gap-3">
            <span class="book-title-mobile px-3 sm:px-4 py-1.5 sm:py-2 bg-black/50 backdrop-blur-md border border-white/10 rounded-full text-white/80 text-xs sm:text-sm font-medium shadow-lg">
                {{ $book->title }}
            </span>
            @auth
                @if($book->file_path)
                    <a href="{{ route('books.download', $book) }}" class="pointer-events-auto flex items-center gap-1.5 px-3 sm:px-4 py-1.5 sm:py-2 bg-teal-500/80 hover:bg-teal-600 backdrop-blur-md rounded-full text-white text-xs sm:text-sm font-medium border border-white/10 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a1 1 0 01-.707-.293l-3-3a1 1 0 011.414-1.414L9 10.586V3a1 1 0 012 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3A1 1 0 0110 12z"/>
                            <path d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                        </svg>
                        <span class="hidden sm:inline">Download</span>
                    </a>
                @endif
            @endauth
        </div>
    </div>

    <!-- DFlip Viewer Container -->
    <div class="reader-container">
        <div class="_df_book" id="pdf-book" 
             webgl="true" 
             backgroundcolor="transparent" 
             source="{{ route('books.stream', $book) }}">
        </div>
    </div>

    <!-- jQuery & DFlip JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dflip/js/dflip.min.js"></script>
</body>
</html>
