<div class="min-h-screen font-roboto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- PAGE HERO HEADER --}}
        <div class="relative text-center mb-12 overflow-hidden">
            {{-- Decorative blobs --}}
            <div class="pointer-events-none absolute -top-16 left-1/2 -translate-x-1/2 w-[600px] h-48 bg-teal-400/20 dark:bg-teal-500/10 blur-3xl rounded-full"></div>
            <div class="pointer-events-none absolute -top-8 left-1/4 w-40 h-40 bg-blue-300/20 dark:bg-blue-500/10 blur-2xl rounded-full"></div>
            <div class="pointer-events-none absolute -top-8 right-1/4 w-40 h-40 bg-emerald-300/20 dark:bg-emerald-500/10 blur-2xl rounded-full"></div>

            {{-- Tag --}}
            <div class="relative inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-teal-50 dark:bg-teal-900/40 text-teal-700 dark:text-teal-400 text-xs font-bold uppercase tracking-widest mb-5 border border-teal-200 dark:border-teal-800 shadow-sm">
                <flux:icon.book-open class="w-3.5 h-3.5" />
                {{ __('global.monitor_perpustakaan') }}
            </div>

            {{-- Title --}}
            <h1 class="relative text-3xl min-[400px]:text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight leading-tight mb-4">
                <span class="text-zinc-900 dark:text-white">{{ explode(' ', __('global.monitor_perpustakaan'))[0] }} </span>
                <span class="relative inline-block">
                    <span class="text-teal-500">{{ explode(' ', __('global.monitor_perpustakaan'))[1] ?? '' }}</span>
                    {{-- Underline decoration --}}
                    <svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 200 8" fill="none" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 5.5C50 1.5 100 7.5 199 3" stroke="#14b8a6" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </span>
            </h1>

            {{-- Subtitle --}}
            <p class="relative text-zinc-500 dark:text-zinc-400 text-sm md:text-lg font-light max-w-xl mx-auto mb-6 px-2">
                {{ __('global.read_and_download') }}
            </p>

            {{-- Stats row --}}
            <div class="relative inline-flex items-center divide-x divide-zinc-200 dark:divide-zinc-700 overflow-hidden max-w-full">
                <div class="px-3 sm:px-8 py-2 sm:py-4 flex flex-col items-center">
                    <span class="text-xl sm:text-3xl font-black text-teal-600 dark:text-teal-400">{{ $books->total() }}</span>
                    <span class="text-[10px] uppercase tracking-widest font-bold text-zinc-400 mt-0.5">{{ __('global.books_count') }}</span>
                </div>
                <div class="px-3 sm:px-8 py-2 sm:py-4 flex flex-col items-center">
                    <span class="text-xl sm:text-3xl font-black text-teal-600 dark:text-teal-400">{{ $categories->count() }}</span>
                    <span class="text-[10px] uppercase tracking-widest font-bold text-zinc-400 mt-0.5">{{ __('global.categories_count') }}</span>
                </div>
                <div class="px-3 sm:px-8 py-2 sm:py-4 flex flex-col items-center hidden min-[360px]:flex">
                    <div class="flex items-center gap-0.5 sm:gap-1">
                        <flux:icon.star class="w-4 h-4 sm:w-5 sm:h-5 text-teal-400" variant="solid" />
                        <flux:icon.star class="w-4 h-4 sm:w-5 sm:h-5 text-teal-400" variant="solid" />
                        <flux:icon.star class="w-4 h-4 sm:w-5 sm:h-5 text-teal-400" variant="solid" />
                        <flux:icon.star class="w-4 h-4 sm:w-5 sm:h-5 text-teal-400" variant="solid" />
                        <flux:icon.star class="w-4 h-4 sm:w-5 sm:h-5 text-teal-400" variant="solid" />
                    </div>
                    <span class="text-[10px] uppercase tracking-widest font-bold text-zinc-400 mt-1">{{ __('global.top_rated') }}</span>
                </div>
            </div>
        </div>

        {{-- STICKY CONTROL BAR --}}
        <div class="sticky top-20 z-30 mb-8">
            <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <flux:input 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="{{ __('global.search_books_placeholder') }}" 
                            icon="magnifying-glass"
                        />
                    </div>
                    <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 w-full sm:w-auto">
                        <div class="w-full sm:w-44 flex-1">
                            <flux:select wire:model.live="selectedCategory">
                                <flux:select.option value="">{{ __('global.all_categories') }}</flux:select.option>
                                @foreach($categories as $category)
                                    <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div class="w-full sm:w-44 flex-1">
                            <flux:select wire:model.live="sortBy">
                                <flux:select.option value="recent">{{ __('global.recent') }}</flux:select.option>
                                <flux:select.option value="popular">{{ __('global.popular') }}</flux:select.option>
                                <flux:select.option value="title">{{ __('global.title_az') }}</flux:select.option>
                            </flux:select>
                        </div>
                        <div class="flex gap-1 bg-zinc-100 dark:bg-zinc-800 rounded-xl p-1 shrink-0 ml-auto sm:ml-0">
                            <button wire:click="$set('viewMode', 'grid')" class="p-2 rounded-lg transition-colors {{ $viewMode === 'grid' ? 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-900 dark:text-white' : 'text-zinc-400 hover:text-zinc-600' }}">
                                <flux:icon.squares-2x2 class="w-4 h-4" />
                            </button>
                            <button wire:click="$set('viewMode', 'list')" class="p-2 rounded-lg transition-colors {{ $viewMode === 'list' ? 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-900 dark:text-white' : 'text-zinc-400 hover:text-zinc-600' }}">
                                <flux:icon.list-bullet class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
                @if($search || $selectedCategory || $sortBy !== 'recent')
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        <p class="text-sm text-zinc-500">{{ __('global.results_found', ['count' => $books->total()]) }}</p>
                        <button wire:click="clearFilters" class="text-xs font-bold text-teal-600 hover:text-teal-700 underline">{{ __('global.clear_filters') }}</button>
                    </div>
                @endif
            </div>
        </div>

        {{-- BOOKS DISPLAY --}}
        @if($books->count() > 0)
            @if($viewMode === 'grid')
                {{-- GRID VIEW (Masonry Layout) --}}
                <div class="columns-2 sm:columns-3 md:columns-4 lg:columns-5 xl:columns-6 gap-5 space-y-5 mb-10">
                    @foreach($books as $book)
                        <a href="{{ route('books.show', $book) }}" wire:key="book-{{ $book->id }}" class="group block break-inside-avoid">
                            {{-- Book Cover --}}
                            <div class="relative rounded-xl overflow-hidden mb-3 shadow-md group-hover:shadow-xl transition-all duration-300 bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center border border-zinc-100 dark:border-zinc-800">
                                @if($book->cover_image)
                                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-auto object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full aspect-[2/3] bg-gradient-to-br from-blue-500 to-teal-400 flex flex-col items-center justify-center p-4">
                                        <flux:icon.book-open class="w-10 h-10 text-white opacity-80 mb-2" />
                                        <p class="text-white text-xs font-bold text-center line-clamp-3 leading-tight">{{ $book->title }}</p>
                                    </div>
                                @endif

                                {{-- Hover overlay --}}
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center gap-2 p-3">
                                    <span class="text-white text-xs font-black uppercase tracking-widest">{{ __('global.view_detail') }}</span>
                                    @if($book->reviews->count() > 0)
                                        <div class="flex items-center gap-1">
                                            <flux:icon.star class="w-3.5 h-3.5 text-teal-400" variant="solid" />
                                            <span class="text-white text-xs font-bold">{{ number_format($book->average_rating, 1) }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Download count --}}
                                <div class="absolute top-2 right-2 bg-black/70 backdrop-blur-sm text-white text-[10px] px-2 py-0.5 rounded-full font-bold flex items-center gap-1">
                                    <flux:icon.arrow-down-tray class="w-3 h-3" />
                                    {{ $book->download_count }}
                                </div>

                                @if($book->category)
                                    <div class="absolute bottom-2 left-2">
                                        <span class="bg-teal-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">{{ $book->category->name }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="px-0.5">
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-white line-clamp-2 leading-tight mb-1 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">{{ $book->title }}</h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">{{ $book->author }}</p>
                                @if($book->reviews->count() > 0)
                                    <div class="flex items-center gap-1 mt-1.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <flux:icon.star class="w-3 h-3 {{ $i <= $book->average_rating ? 'text-teal-400' : 'text-zinc-300' }}" variant="{{ $i <= $book->average_rating ? 'solid' : 'outline' }}" />
                                        @endfor
                                        <span class="text-[10px] text-zinc-400 ml-0.5">({{ $book->reviews->count() }})</span>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                {{-- LIST VIEW --}}
                <div class="space-y-3 mb-10">
                    @foreach($books as $book)
                        <div wire:key="book-list-{{ $book->id }}" class="group bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 hover:shadow-lg hover:border-teal-200 dark:hover:border-teal-800 transition-all duration-300">
                            <div class="flex gap-5 items-center">
                                {{-- Cover --}}
                                <a href="{{ route('books.show', $book) }}" class="w-20 rounded-xl overflow-hidden shrink-0 shadow-md bg-zinc-50 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 flex items-center justify-center">
                                    @if($book->cover_image)
                                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-auto object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full aspect-[2/3] bg-gradient-to-br from-blue-500 to-teal-400 flex items-center justify-center">
                                            <flux:icon.book-open class="w-6 h-6 text-white" />
                                        </div>
                                    @endif
                                </a>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <a href="{{ route('books.show', $book) }}">
                                                <h3 class="font-bold text-zinc-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors mb-1 truncate">{{ $book->title }}</h3>
                                            </a>
                                            <p class="text-sm text-zinc-500 mb-2">oleh {{ $book->author }}</p>
                                            @if($book->category)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-50 dark:bg-teal-900/40 text-teal-700 dark:text-teal-400">{{ $book->category->name }}</span>
                                            @endif
                                        </div>
                                        <div class="shrink-0 text-right">
                                            @if($book->reviews->count() > 0)
                                                <div class="flex items-center gap-1 justify-end mb-1">
                                                    <flux:icon.star class="w-4 h-4 text-teal-400" variant="solid" />
                                                    <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ number_format($book->average_rating, 1) }}</span>
                                                </div>
                                            @endif
                                            <div class="flex items-center gap-1 text-zinc-400">
                                                <flux:icon.arrow-down-tray class="w-3.5 h-3.5" />
                                                <span class="text-xs font-medium">{{ $book->download_count }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="text-sm text-zinc-500 dark:text-zinc-400 line-clamp-1 mt-2">{{ $book->description }}</p>
                                </div>

                                {{-- Actions --}}
                                <div class="flex gap-2 shrink-0">
                                    <flux:button href="{{ route('books.show', $book) }}" variant="ghost" size="sm" class="rounded-xl!">{{ __('global.detail') ?? 'Detail' }}</flux:button>
                                    @auth
                                        @if($book->file_path)
                                            <flux:button href="{{ route('books.download', $book) }}" icon="arrow-down-tray" size="sm" variant="primary" class="rounded-xl!" />
                                        @endif
                                    @else
                                        <flux:button href="{{ route('login') }}" size="sm" variant="ghost" class="rounded-xl!" icon="lock-closed" />
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Pagination --}}
            <div class="flex justify-center">
                <div class="bg-white dark:bg-zinc-900 p-2 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-800">
                    {{ $books->links() }}
                </div>
            </div>

        @else
            {{-- EMPTY STATE --}}
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-32 h-32 rounded-full bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center mb-6">
                    <flux:icon.book-open class="w-14 h-14 text-zinc-300 dark:text-zinc-700" />
                </div>
                <h3 class="text-2xl font-black text-zinc-900 dark:text-white mb-2">{{ __('global.no_books_found') }}</h3>
                <p class="text-zinc-500 max-w-sm mb-6">{{ __('global.no_books_description') }}</p>
                @if($search || $selectedCategory || $sortBy !== 'recent')
                    <flux:button wire:click="clearFilters" variant="primary" class="rounded-xl!">{{ __('global.clear_filters') }}</flux:button>
                @endif
            </div>
        @endif
    </div>
</div>