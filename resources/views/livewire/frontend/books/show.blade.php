<div class="min-h-screen bg-gradient-to-br from-slate-50 to-mint-50/20 dark:from-slate-900 dark:to-slate-800">
    <div class="container mx-auto px-4 py-8">
        <!-- Back Button -->
        <div class="mb-6">
            <flux:button href="{{ route('books.index') }}" variant="ghost" icon="arrow-left">
                Back to Library
            </flux:button>
        </div>

        <!-- Book Details -->
        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-8">
                <!-- Cover Image -->
                <div class="lg:col-span-1">
                    <div class="aspect-[3/4] bg-gradient-to-br from-mint-100 to-mint-200 dark:from-mint-900/20 dark:to-mint-800/20 rounded-xl overflow-hidden relative">
                        @if($book->cover_image)
                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <flux:icon name="book-open" class="w-24 h-24 text-mint-600 dark:text-mint-400" />
                            </div>
                        @endif
                        
                        <!-- Download Count Badge -->
                        <div class="absolute top-4 right-4 bg-mint-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                            {{ $book->download_count }} downloads
                        </div>
                    </div>
                </div>

                <!-- Book Information -->
                <div class="lg:col-span-2">
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold tracking-tighter text-balance text-slate-900 dark:text-slate-100 mb-2">
                            {{ $book->title }}
                        </h1>
                        
                        <p class="text-xl text-slate-600 dark:text-slate-400 mb-4">
                            by {{ $book->author }}
                        </p>

                        @if($book->category)
                            <flux:badge variant="outline" class="mb-4">
                                {{ $book->category->name }}
                            </flux:badge>
                        @endif

                        <!-- Rating -->
                        @if($book->reviews->count() > 0)
                            <div class="flex items-center gap-2 mb-4">
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <flux:icon 
                                            name="star" 
                                            class="w-5 h-5 {{ $i <= $book->average_rating ? 'text-mint-500' : 'text-slate-300' }}"
                                            variant="{{ $i <= $book->average_rating ? 'solid' : 'outline' }}"
                                        />
                                    @endfor
                                </div>
                                <span class="text-slate-600 dark:text-slate-400">
                                    {{ number_format($book->average_rating, 1) }} out of 5 
                                    ({{ $book->reviews->count() }} {{ Str::plural('review', $book->reviews->count()) }})
                                </span>
                            </div>
                        @endif

                        <p class="text-slate-700 dark:text-slate-300 leading-relaxed mb-6">
                            {{ $book->description }}
                        </p>

                        <!-- Metadata -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 text-sm">
                            <div>
                                <span class="font-medium text-slate-900 dark:text-slate-100">Added:</span>
                                <span class="text-slate-600 dark:text-slate-400">{{ $book->created_at->format('M j, Y') }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-slate-900 dark:text-slate-100">Downloads:</span>
                                <span class="text-slate-600 dark:text-slate-400">{{ number_format($book->download_count) }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap gap-3">
                            @auth
                                @if($book->file_path)
                                    <flux:button 
                                        href="{{ route('books.download', $book) }}" 
                                        variant="primary" 
                                        icon="arrow-down-tray"
                                        class="bg-mint-600 hover:bg-mint-700"
                                    >
                                        Download Book
                                    </flux:button>
                                @endif
                                
                                @if($userReview)
                                    <flux:button wire:click="toggleReviewForm" variant="outline">
                                        Edit My Review
                                    </flux:button>
                                    <flux:button wire:click="deleteReview" variant="danger" wire:confirm="Are you sure you want to delete your review?">
                                        Delete Review
                                    </flux:button>
                                @else
                                    <flux:button wire:click="toggleReviewForm" variant="outline">
                                        Write a Review
                                    </flux:button>
                                @endif
                            @else
                                <flux:button href="{{ route('login') }}" variant="primary" icon="arrow-down-tray">
                                    Login to Download
                                </flux:button>
                                <flux:button href="{{ route('login') }}" variant="outline">
                                    Login to Review
                                </flux:button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Form -->
        @auth
            @if($showReviewForm)
                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 mb-8">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 mb-4">
                        {{ $userReview ? 'Edit Your Review' : 'Write a Review' }}
                    </h3>

                    <form wire:submit="submitReview" class="space-y-4">
                        <!-- Rating -->
                        <div>
                            <flux:label>Rating</flux:label>
                            <div class="flex items-center gap-1 mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <button 
                                        type="button"
                                        wire:click="$set('rating', {{ $i }})"
                                        class="focus:outline-none transition-colors duration-200"
                                    >
                                        <flux:icon 
                                            name="star" 
                                            class="w-8 h-8 {{ $i <= $rating ? 'text-mint-500 hover:text-mint-600' : 'text-slate-300 hover:text-slate-400' }}"
                                            variant="{{ $i <= $rating ? 'solid' : 'outline' }}"
                                        />
                                    </button>
                                @endfor
                                <span class="ml-2 text-slate-600 dark:text-slate-400">
                                    {{ $rating }} out of 5 stars
                                </span>
                            </div>
                            <flux:error name="rating" />
                        </div>

                        <!-- Comment -->
                        <div>
                            <flux:label>Comment (optional)</flux:label>
                            <flux:textarea 
                                wire:model="comment" 
                                placeholder="Share your thoughts about this book..."
                                rows="4"
                                class="mt-1"
                            />
                            <flux:error name="comment" />
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-3">
                            <flux:button type="submit" variant="primary">
                                {{ $userReview ? 'Update Review' : 'Submit Review' }}
                            </flux:button>
                            <flux:button type="button" wire:click="toggleReviewForm" variant="ghost">
                                Cancel
                            </flux:button>
                        </div>
                    </form>
                </div>
            @endif
        @endauth

        <!-- Reviews Section -->
        @if($book->reviews->count() > 0)
            <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6">
                <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 mb-6">
                    Reviews ({{ $book->reviews->count() }})
                </h3>

                <div class="space-y-6">
                    @foreach($book->reviews->sortByDesc('created_at') as $review)
                        <div class="border-b border-slate-200 dark:border-slate-700 last:border-b-0 pb-6 last:pb-0">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h4 class="font-medium text-slate-900 dark:text-slate-100">
                                        {{ $review->user->name }}
                                    </h4>
                                    <div class="flex items-center gap-1 mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <flux:icon 
                                                name="star" 
                                                class="w-4 h-4 {{ $i <= $review->rating ? 'text-mint-500' : 'text-slate-300' }}"
                                                variant="{{ $i <= $review->rating ? 'solid' : 'outline' }}"
                                            />
                                        @endfor
                                    </div>
                                </div>
                                <span class="text-sm text-slate-500 dark:text-slate-400">
                                    {{ $review->created_at->format('M j, Y') }}
                                </span>
                            </div>

                            @if($review->comment)
                                <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                                    {{ $review->comment }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 text-center">
                <flux:icon name="chat-bubble-left-ellipsis" class="w-12 h-12 text-slate-400 mx-auto mb-3" />
                <h3 class="text-lg font-medium text-slate-900 dark:text-slate-100 mb-2">
                    No reviews yet
                </h3>
                <p class="text-slate-600 dark:text-slate-400">
                    Be the first to review this book!
                </p>
            </div>
        @endif

        <!-- Success Message -->
        @if(session('success'))
            <div class="fixed top-4 right-4 bg-mint-600 text-white px-4 py-2 rounded-lg shadow-lg z-50">
                {{ session('success') }}
            </div>
        @endif
    </div>
</div>