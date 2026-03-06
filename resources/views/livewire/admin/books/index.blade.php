<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Manage Books</flux:heading>
        <flux:button href="{{ route('admin.books.create') }}" variant="primary" icon="plus">
            Add New Book
        </flux:button>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search books..." 
                icon="magnifying-glass"
            />
            
            <flux:select wire:model.live="selectedCategory" placeholder="All Categories">
                <flux:select.option value="">All Categories</flux:select.option>
                @foreach($categories as $category)
                    <flux:select.option value="{{ $category->id }}">
                        {{ $category->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="sortBy">
                <flux:select.option value="recent">Recently Added</flux:select.option>
                <flux:select.option value="popular">Most Popular</flux:select.option>
                <flux:select.option value="title">Title A-Z</flux:select.option>
            </flux:select>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Books Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Book</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Downloads</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Added</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($books as $book)
                        <tr wire:key="book-{{ $book->id }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-16 bg-gradient-to-br from-mint-100 to-mint-200 dark:from-mint-900/20 dark:to-mint-800/20 rounded flex-shrink-0 mr-4 overflow-hidden">
                                        @if($book->cover_image)
                                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <flux:icon name="book-open" class="w-6 h-6 text-mint-600 dark:text-mint-400" />
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $book->title }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            by {{ $book->author }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                @if($book->category)
                                    <flux:badge variant="outline">{{ $book->category->name }}</flux:badge>
                                @else
                                    <span class="text-gray-400">No category</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ number_format($book->download_count) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                @if($book->reviews->count() > 0)
                                    <div class="flex items-center">
                                        <flux:icon name="star" class="w-4 h-4 text-mint-500 mr-1" variant="solid" />
                                        {{ number_format($book->average_rating, 1) }}
                                        <span class="text-gray-400 ml-1">({{ $book->reviews->count() }})</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">No reviews</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $book->created_at->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <flux:button 
                                    href="{{ route('books.show', $book) }}" 
                                    size="sm" 
                                    variant="ghost"
                                    target="_blank"
                                >
                                    View
                                </flux:button>
                                <flux:button 
                                    href="{{ route('admin.books.edit', $book) }}" 
                                    size="sm"
                                >
                                    Edit
                                </flux:button>
                                <flux:button 
                                    size="sm" 
                                    variant="danger" 
                                    wire:click="deleteBook({{ $book->id }})"
                                    wire:confirm="Are you sure you want to delete this book? This action cannot be undone."
                                >
                                    Delete
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No books found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $books->links() }}
        </div>
    </div>
</div>