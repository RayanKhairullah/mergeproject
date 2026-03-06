

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-mint-50/20 dark:from-slate-900 dark:to-slate-800">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold tracking-tighter text-balance text-slate-900 dark:text-slate-100 mb-4">
                Digital Library
            </h1>
            <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                Discover, download, and review our collection of digital books and resources
            </p>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <flux:input 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search books by title or author..." 
                        icon="magnifying-glass"
                        class="w-full"
                    />
                </div>

                <!-- Category Filter -->
                <div>
                    <flux:select wire:model.live="selectedCategory" placeholder="All Categories">
                        <flux:select.option value="">All Categories</flux:select.option>
                        @foreach($categories as $category)
                            <flux:select.option value="{{ $category->id }}">
                                {{ $category->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Sort -->
                <div>
                    <flux:select wire:model.live="sortBy">
                        <flux:select.option value="recent">Recently Added</flux:select.option>
                        <flux:select.option value="popular">Most Popular</flux:select.option>
                        <flux:select.option value="title">Title A-Z</flux:select.option>
                    </flux:select>
                </div>
            </div>

            <!-- View Mode and Clear Filters -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:button 
                        wire:click="$set('viewMode', 'grid')" 
                        variant="{{ $viewMode === 'grid' ? 'primary' : 'ghost' }}"
                        size="sm"
                        icon="squares-2x2"
                    >
                        Grid
                    </flux:button>
                    <flux:button 
                        wire:click="$set('viewMode', 'list')" 
                        variant="{{ $viewMode === 'list' ? 'primary' : 'ghost' }}"
                        size="sm"
                        icon="list-bullet"
                    >
                        List
                    </flux:button>
                </div>

                @if($search || $selectedCategory || $sortBy !== 'recent')
                    <flux:button wire:click="clearFilters" variant="ghost" size="sm">
                        Clear Filters
                    </flux:button>
                @endif
            </div>
        </div>

        <!-- Results Count -->
        <div class="mb-6">
            <p class="text-slate-600 dark:text-slate-400">
                Showing {{ $books->count() }} of {{ $books->total() }} books
            </p>
        </div>

        <!-- Books Grid/List -->
        @if($books->count() > 0)
            <div class="{{ $viewMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6' : 'space-y-4' }} mb-8">
                @foreach($books as $book)
                    <div wire:key="book-{{ $book->id }}" class="group">
                        @if($viewMode === 'grid')
                            <!-- Grid View Card -->
                            <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden hover:shadow-xl transition-all duration-750 ease-in-out hover:scale-105">
                                <!-- Cover Image -->
                                <div class="aspect-[3/4] bg-gradient-to-br from-mint-100 to-mint-200 dark:from-mint-900/20 dark:to-mint-800/20 relative overflow-hidden">
                                    @if($book->cover_image)
                                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <flux:icon name="book-open" class="w-16 h-16 text-mint-600 dark:text-mint-400" />
                                        </div>
                                    @endif
                                    
                                    <!-- Download Count Badge -->
                                    <div class="absolute top-2 right-2 bg-mint-600 text-white text-xs px-2 py-1 rounded-full">
                                        {{ $book->download_count }} downloads
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-4">
                                    <h3 class="font-bold text-lg tracking-tighter text-balance text-slate-900 dark:text-slate-100 mb-2 line-clamp-2">
                                        {{ $book->title }}
                                    </h3>
                                    
                                    <p class="text-slate-600 dark:text-slate-400 text-sm mb-2">
                                        by {{ $book->author }}
                                    </p>

                                    @if($book->category)
                                        <flux:badge variant="outline" class="mb-3">
                                            {{ $book->category->name }}
                                        </flux:badge>
                                    @endif

                                    <!-- Rating -->
                                    @if($book->reviews->count() > 0)
                                        <div class="flex items-center gap-1 mb-3">
                                            @for($i = 1; $i <= 5; $i++)
                                                <flux:icon 
                                                    name="star" 
                                                    class="w-4 h-4 {{ $i <= $book->average_rating ? 'text-mint-500' : 'text-slate-300' }}"
                                                    variant="{{ $i <= $book->average_rating ? 'solid' : 'outline' }}"
                                                />
                                            @endfor
                                            <span class="text-sm text-slate-600 dark:text-slate-400 ml-1">
                                                ({{ $book->reviews->count() }})
                                            </span>
                                        </div>
                                    @endif

                                    <p class="text-slate-600 dark:text-slate-400 text-sm line-clamp-2 mb-4">
                                        {{ $book->description }}
                                    </p>

                                    <!-- Actions -->
                                    <div class="flex gap-2">
                                        <flux:button 
                                            href="{{ route('books.show', $book) }}" 
                                            variant="primary" 
                                            size="sm" 
                                            class="flex-1"
                                        >
                                            View Details
                                        </flux:button>
                                        
                                        @auth
                                            @if($book->file_path)
                                                <flux:button 
                                                    href="{{ route('books.download', $book) }}" 
                                                    variant="outline" 
                                                    size="sm"
                                                    icon="arrow-down-tray"
                                                >
                                                    Download
                                                </flux:button>
                                            @endif
                                        @else
                                            <flux:button 
                                                href="{{ route('login') }}" 
                                                variant="outline" 
                                                size="sm"
                                                icon="arrow-down-tray"
                                            >
                                                Login to Download
                                            </flux:button>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- List View Card -->
                            <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl transition-all duration-750 ease-in-out">
                                <div class="flex gap-4">
                                    <!-- Cover Image -->
                                    <div class="w-20 h-28 bg-gradient-to-br from-mint-100 to-mint-200 dark:from-mint-900/20 dark:to-mint-800/20 rounded-lg flex-shrink-0 overflow-hidden">
                                        @if($book->cover_image)
                                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <flux:icon name="book-open" class="w-8 h-8 text-mint-600 dark:text-mint-400" />
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <h3 class="font-bold text-xl tracking-tighter text-balance text-slate-900 dark:text-slate-100 mb-1">
                                                    {{ $book->title }}
                                                </h3>
                                                <p class="text-slate-600 dark:text-slate-400">
                                                    by {{ $book->author }}
                                                </p>
                                            </div>
                                            
                                            <div class="text-right">
                                                <div class="text-sm text-mint-600 dark:text-mint-400 font-medium">
                                                    {{ $book->download_count }} downloads
                                                </div>
                                                @if($book->category)
                                                    <flux:badge variant="outline" class="mt-1">
                                                        {{ $book->category->name }}
                                                    </flux:badge>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Rating -->
                                        @if($book->reviews->count() > 0)
                                            <div class="flex items-center gap-1 mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <flux:icon 
                                                        name="star" 
                                                        class="w-4 h-4 {{ $i <= $book->average_rating ? 'text-mint-500' : 'text-slate-300' }}"
                                                        variant="{{ $i <= $book->average_rating ? 'solid' : 'outline' }}"
                                                    />
                                                @endfor
                                                <span class="text-sm text-slate-600 dark:text-slate-400 ml-1">
                                                    ({{ $book->reviews->count() }} reviews)
                                                </span>
                                            </div>
                                        @endif

                                        <p class="text-slate-600 dark:text-slate-400 text-sm mb-4 line-clamp-2">
                                            {{ $book->description }}
                                        </p>

                                        <!-- Actions -->
                                        <div class="flex gap-2">
                                            <flux:button 
                                                href="{{ route('books.show', $book) }}" 
                                                variant="primary" 
                                                size="sm"
                                            >
                                                View Details
                                            </flux:button>
                                            
                                            @auth
                                                @if($book->file_path)
                                                    <flux:button 
                                                        href="{{ route('books.download', $book) }}" 
                                                        variant="outline" 
                                                        size="sm"
                                                        icon="arrow-down-tray"
                                                    >
                                                        Download
                                                    </flux:button>
                                                @endif
                                            @else
                                                <flux:button 
                                                    href="{{ route('login') }}" 
                                                    variant="outline" 
                                                    size="sm"
                                                    icon="arrow-down-tray"
                                                >
                                                    Login to Download
                                                </flux:button>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $books->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <flux:icon name="book-open" class="w-24 h-24 text-slate-400 mx-auto mb-4" />
                <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100 mb-2">
                    No books found
                </h3>
                <p class="text-slate-600 dark:text-slate-400 mb-4">
                    Try adjusting your search criteria or browse all categories.
                </p>
                @if($search || $selectedCategory || $sortBy !== 'recent')
                    <flux:button wire:click="clearFilters" variant="primary">
                        Clear Filters
                    </flux:button>
                @endif
            </div>
        @endif
    </div>
</div>