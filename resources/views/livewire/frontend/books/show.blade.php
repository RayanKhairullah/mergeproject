<div class="min-h-screen font-roboto">
    @push('meta')
        <meta property="og:title" content="{{ $book->title }} - {{ config('app.name') }}" />
        <meta property="og:description" content="{{ Str::limit($book->description, 150) }}" />
        <meta property="og:type" content="article" />
        <meta property="og:url" content="{{ request()->url() }}" />
        @if($book->cover_image)
            <meta property="og:image" content="{{ asset('storage/' . $book->cover_image) }}" />
            <!-- WhatsApp recommends 300KB max, but 1200x630px is optimal for wide view -->
            <meta property="og:image:width" content="1200" />
            <meta property="og:image:height" content="630" />
        @else
            <!-- Fallback generic open graph image if you have one -->
            <meta property="og:image" content="{{ asset('images/logo-primer.png') }}" />
        @endif
    @endpush

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10" x-data="{ showReviewForm: @entangle('showReviewForm') }">

        {{-- BACK BUTTON --}}
        <div class="mb-6">
            <flux:button href="{{ route('books.index') }}" variant="ghost" icon="arrow-left" size="sm" class="text-zinc-500 hover:text-zinc-900 dark:hover:text-white">
                <span class="hidden sm:inline">{{ __('global.back_to_library') ?? 'Kembali ke Perpustakaan' }}</span>
                <span class="sm:hidden">{{ __('global.back') ?? 'Kembali' }}</span>
            </flux:button>
        </div>

        {{-- MOBILE: Cover First --}}
        <div class="lg:hidden mb-6">
            <div class="flex flex-col sm:flex-row gap-5">
                {{-- Cover --}}
                <div class="w-full max-w-[160px] sm:max-w-[200px] mx-auto sm:mx-0 shrink-0 rounded-2xl overflow-hidden shadow-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800">
                    @if($book->cover_image)
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-auto object-cover">
                    @else
                        <div class="w-full aspect-[2/3] bg-gradient-to-br from-blue-500 to-teal-400 flex flex-col items-center justify-center p-4">
                            <flux:icon.book-open class="w-12 h-12 text-white opacity-80 mb-2" />
                            <p class="text-white text-xs font-bold text-center leading-tight">{{ $book->title }}</p>
                        </div>
                    @endif
                </div>

                {{-- Mobile Stats Row --}}
                <div class="flex-1 space-y-2">
                    @if($book->category)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400">{{ $book->category->name }}</span>
                    @endif
                    
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white leading-tight">{{ $book->title }}</h1>
                    <p class="text-base text-zinc-500 dark:text-zinc-400 font-light">{{ __('global.by') ?? 'oleh' }} <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $book->author }}</span></p>
                    
                    {{-- Quick Stats --}}
                    <div class="flex flex-wrap gap-2 mt-3">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-zinc-900 rounded-full border border-zinc-200 dark:border-zinc-800 text-xs">
                            <flux:icon.arrow-down-tray class="w-3.5 h-3.5 text-teal-500" />
                            <span class="font-bold">{{ number_format($book->download_count) }}</span>
                        </div>
                        @if($book->reviews->count() > 0)
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-zinc-900 rounded-full border border-zinc-200 dark:border-zinc-800 text-xs">
                                <flux:icon.star class="w-3.5 h-3.5 text-teal-400" variant="solid" />
                                <span class="font-bold">{{ number_format($book->average_rating, 1) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- BOOK DETAIL (Desktop) --}}
        <div class="hidden lg:grid grid-cols-1 lg:grid-cols-12 gap-10 mb-10">

            {{-- Cover Column --}}
            <div class="lg:col-span-3 flex flex-col items-center lg:items-start gap-5">
                {{-- Cover --}}
                <div class="w-full max-w-[220px] rounded-2xl overflow-hidden shadow-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 flex items-center justify-center">
                    @if($book->cover_image)
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-auto object-cover">
                    @else
                        <div class="w-full aspect-[2/3] bg-gradient-to-br from-blue-500 to-teal-400 flex flex-col items-center justify-center p-6">
                            <flux:icon.book-open class="w-16 h-16 text-white opacity-80 mb-3" />
                            <p class="text-white text-sm font-bold text-center leading-tight">{{ $book->title }}</p>
                        </div>
                    @endif
                </div>

                {{-- Stats --}}
                <div class="w-full max-w-[220px] space-y-3">
                    <div class="flex items-center justify-between py-2.5 px-4 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
                        <span class="text-xs text-zinc-400 font-bold uppercase tracking-wider">{{ __('global.downloads') ?? 'Unduhan' }}</span>
                        <div class="flex items-center gap-1.5 text-teal-600 dark:text-teal-400">
                            <flux:icon.arrow-down-tray class="w-3.5 h-3.5" />
                            <span class="font-black text-sm">{{ number_format($book->download_count) }}</span>
                        </div>
                    </div>
                    @if($book->reviews->count() > 0)
                        <div class="flex items-center justify-between py-2.5 px-4 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
                            <span class="text-xs text-zinc-400 font-bold uppercase tracking-wider">{{ __('global.rating') }}</span>
                            <div class="flex items-center gap-1.5 text-teal-500">
                                <flux:icon.star class="w-3.5 h-3.5" variant="solid" />
                                <span class="font-black text-sm text-zinc-900 dark:text-white">{{ number_format($book->average_rating, 1) }}/5</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between py-2.5 px-4 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
                            <span class="text-xs text-zinc-400 font-bold uppercase tracking-wider">{{ __('global.reviews') ?? 'Ulasan' }}</span>
                            <span class="font-black text-sm text-zinc-900 dark:text-white">{{ $book->reviews->count() }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between py-2.5 px-4 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
                        <span class="text-xs text-zinc-400 font-bold uppercase tracking-wider">{{ __('global.added_at') ?? 'Ditambahkan' }}</span>
                        <span class="font-bold text-xs text-zinc-600 dark:text-zinc-400">{{ $book->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Info Column (Desktop) --}}
            <div class="lg:col-span-9">
                @if($book->category)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 mb-4">{{ $book->category->name }}</span>
                @endif

                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-zinc-900 dark:text-white leading-tight mb-2">{{ $book->title }}</h1>
                <p class="text-lg text-zinc-500 dark:text-zinc-400 font-light mb-6">{{ __('global.by') ?? 'oleh' }} <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $book->author }}</span></p>

                @if($book->reviews->count() > 0)
                    <div class="flex items-center gap-3 mb-8">
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <flux:icon.star class="w-5 h-5 {{ $i <= $book->average_rating ? 'text-teal-400' : 'text-zinc-300' }}" variant="{{ $i <= $book->average_rating ? 'solid' : 'outline' }}" />
                            @endfor
                        </div>
                        <span class="text-zinc-500 dark:text-zinc-400 text-sm font-medium">{{ number_format($book->average_rating, 1) }} {{ __('global.out_of_5_stars') ?? 'dari 5 bintang' }} ({{ $book->reviews->count() }} {{ __('global.reviews') ?? 'ulasan' }})</span>
                    </div>
                @endif

                <div class="prose prose-zinc dark:prose-invert max-w-none mb-8">
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed text-base">{{ $book->description }}</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    @if($book->file_path)
                        <flux:button 
                            href="{{ route('books.read', $book) }}" 
                            variant="primary" 
                            icon="book-open"
                            class="rounded-xl! bg-teal-500 hover:bg-teal-600 border-0! px-6"
                        >
                            {{ __('global.read_book') ?? 'Baca Buku' }}
                        </flux:button>
                        <flux:button 
                            href="{{ route('books.download', $book) }}" 
                            variant="primary" 
                            icon="arrow-down-tray"
                            class="rounded-xl! bg-blue-500 hover:bg-blue-600 border-0! px-6"
                        >
                            {{ __('global.download_pdf') ?? 'Unduh PDF' }}
                        </flux:button>
                    @endif

                    @if($userReview)
                        @if($userReview->canBeEdited())
                            <flux:button @click="showReviewForm = !showReviewForm" variant="ghost" class="rounded-xl!" icon="pencil-square">{{ __('global.edit_my_review') ?? 'Edit Ulasan Saya' }}</flux:button>
                            <flux:button wire:click="deleteReview" variant="danger" class="rounded-xl!" wire:confirm="{{ __('global.confirm_delete_review') ?? 'Yakin ingin menghapus ulasan ini?' }}">{{ __('global.delete_review') ?? 'Hapus Ulasan' }}</flux:button>
                        @else
                            <div class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 rounded-xl text-sm font-medium flex items-center gap-2">
                                <flux:icon.check-circle class="w-4 h-4 text-teal-500" />
                                {{ __('global.review_submitted') ?? 'Ulasan Terkirim' }}
                            </div>
                        @endif
                    @else
                        <flux:button @click="showReviewForm = !showReviewForm" variant="ghost" class="rounded-xl!" icon="chat-bubble-left-ellipsis">{{ __('global.write_review') ?? 'Tulis Ulasan' }}</flux:button>
                    @endif
                </div>
            </div>
        </div>

        {{-- MOBILE: Description & Actions --}}
        <div class="lg:hidden mb-6">
            @if($book->reviews->count() > 0)
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <flux:icon.star class="w-4 h-4 {{ $i <= $book->average_rating ? 'text-teal-400' : 'text-zinc-300' }}" variant="{{ $i <= $book->average_rating ? 'solid' : 'outline' }}" />
                        @endfor
                    </div>
                    <span class="text-zinc-500 dark:text-zinc-400 text-xs">({{ $book->reviews->count() }} {{ __('global.reviews') ?? 'ulasan' }})</span>
                </div>
            @endif

            <div class="prose prose-zinc dark:prose-invert max-w-none mb-5">
                <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $book->description }}</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                @if($book->file_path)
                    <flux:button 
                        href="{{ route('books.read', $book) }}" 
                        variant="primary" 
                        icon="book-open"
                        class="flex-1 rounded-xl! bg-teal-500 hover:bg-teal-600 border-0!"
                    >
                        {{ __('global.read_book') ?? 'Baca Buku' }}
                    </flux:button>
                    <flux:button 
                        href="{{ route('books.download', $book) }}" 
                        variant="primary" 
                        icon="arrow-down-tray"
                        class="flex-1 rounded-xl! bg-blue-500 hover:bg-blue-600 border-0!"
                    >
                        {{ __('global.download_pdf') ?? 'Unduh PDF' }}
                    </flux:button>
                @endif
            </div>

            {{-- Mobile Review Button --}}
            <div class="mt-4">
                @if($userReview)
                    @if($userReview->canBeEdited())
                        <flux:button @click="showReviewForm = !showReviewForm" variant="ghost" class="rounded-xl! w-full" icon="pencil-square">{{ __('global.edit_my_review') ?? 'Edit Ulasan Saya' }}</flux:button>
                    @else
                        <div class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 rounded-xl text-sm font-medium flex items-center justify-center gap-2">
                            <flux:icon.check-circle class="w-4 h-4 text-teal-500" />
                            {{ __('global.review_submitted') ?? 'Ulasan Terkirim' }}
                        </div>
                    @endif
                @else
                    <flux:button @click="showReviewForm = !showReviewForm" variant="ghost" class="rounded-xl! w-full" icon="chat-bubble-left-ellipsis">{{ __('global.write_review') ?? 'Tulis Ulasan' }}</flux:button>
                @endif
            </div>
        </div>

        {{-- REVIEW FORM --}}
        <div x-show="showReviewForm" x-transition.opacity x-cloak style="display: none;">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-5 sm:p-8 mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-bold text-zinc-900 dark:text-white mb-4 sm:mb-6 flex items-center gap-2">
                    <flux:icon.pencil-square class="w-5 h-5 text-teal-500" />
                    {{ $userReview ? (__('global.edit_your_review') ?? 'Edit Ulasan Anda') : (__('global.write_review') ?? 'Tulis Ulasan') }}
                </h3>

                <form wire:submit="submitReview" class="space-y-4 sm:space-y-6">
                    {{-- Star Rating --}}
                    <div>
                        <label class="block text-[10px] sm:text-xs font-black uppercase tracking-widest text-zinc-500 mb-2 sm:mb-3">Rating</label>
                        <div class="flex items-center gap-1 sm:gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <button 
                                    type="button"
                                    wire:click.prevent="setRating({{ $i }})"
                                    class="focus:outline-none transform hover:scale-110 transition-transform duration-150"
                                >
                                    <flux:icon.star class="w-7 h-7 sm:w-9 sm:h-9 {{ $i <= $rating ? 'text-teal-400 drop-shadow-sm' : 'text-zinc-300 dark:text-zinc-600' }}" variant="{{ $i <= $rating ? 'solid' : 'outline' }}" />
                                </button>
                            @endfor
                            <span class="ml-1 sm:ml-2 text-xs sm:text-sm text-zinc-500 font-medium">{{ $rating }}/5</span>
                        </div>
                        <flux:error name="rating" />
                    </div>

                    @guest
                        <div class="mb-3 sm:mb-4">
                            <label class="block text-[10px] sm:text-xs font-black uppercase tracking-widest text-zinc-500 mb-2 sm:mb-3">{{ __('global.username') ?? 'Nama' }} <span class="normal-case font-normal text-zinc-400 ml-1">({{ __('global.anonymous') ?? 'Anonim' }})</span></label>
                            <flux:input wire:model="anonymous_name" placeholder="{{ __('global.enter_name') ?? 'Masukkan nama...' }}" />
                        </div>
                    @endguest

                    {{-- Comment --}}
                    <div>
                        <label class="block text-[10px] sm:text-xs font-black uppercase tracking-widest text-zinc-500 mb-2 sm:mb-3">{{ __('global.comment') ?? 'Komentar' }} <span class="normal-case font-normal text-zinc-400 ml-1">({{ __('vehicles.optional') ?? 'Opsional' }})</span></label>
                        <flux:textarea 
                            wire:model="comment" 
                            placeholder="{{ __('global.comment_placeholder') ?? 'Bagikan pendapat Anda tentang buku ini...' }}"
                            rows="3"
                        />
                        <flux:error name="comment" />
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <flux:button type="submit" variant="primary" class="rounded-xl! bg-teal-500 hover:bg-teal-600 border-0! flex-1 sm:flex-none">
                            {{ $userReview ? (__('global.update_review') ?? 'Update Ulasan') : (__('global.submit_review') ?? 'Kirim Ulasan') }}
                        </flux:button>
                        <flux:button type="button" @click="showReviewForm = false" variant="ghost" class="rounded-xl!">{{ __('global.cancel') ?? 'Batal' }}</flux:button>
                    </div>
                </form>
            </div>
        </div>

        {{-- REVIEWS SECTION --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-8 py-4 sm:py-6 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                <h3 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                    <flux:icon.chat-bubble-left-ellipsis class="w-4 h-4 sm:w-5 sm:h-5 text-teal-500" />
                    {{ __('global.reviews') ?? 'Ulasan' }}
                    @if($book->reviews->count() > 0)
                        <span class="text-xs sm:text-sm font-normal text-zinc-400">({{ $book->reviews->count() }})</span>
                    @endif
                </h3>
                @if($book->reviews->count() > 0)
                    <div class="flex items-center gap-1 sm:gap-2">
                        <div class="flex items-center gap-0.5 sm:gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <flux:icon.star class="w-3 h-3 sm:w-4 sm:h-4 {{ $i <= round($book->average_rating) ? 'text-teal-400' : 'text-zinc-300' }}" variant="{{ $i <= round($book->average_rating) ? 'solid' : 'outline' }}" />
                            @endfor
                        </div>
                        <span class="text-xs sm:text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ number_format($book->average_rating, 1) }}</span>
                    </div>
                @endif
            </div>

            @if($book->reviews->count() > 0)
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($book->reviews->sortByDesc('created_at') as $review)
                        <div class="px-4 sm:px-8 py-4 sm:py-6">
                            <div class="flex items-start justify-between mb-2 sm:mb-3">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    @php
                                        $reviewerName = $review->user ? $review->user->name : ($review->anonymous_name ?? (__('global.anonymous') ?? 'Anonim'));
                                    @endphp
                                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-br from-blue-500 to-teal-400 flex items-center justify-center text-white text-xs sm:text-sm font-bold shrink-0">
                                        {{ substr($reviewerName, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-xs sm:text-sm text-zinc-900 dark:text-white">{{ $reviewerName }}</p>
                                        <div class="flex items-center gap-0.5 sm:gap-1 mt-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <flux:icon.star class="w-3 h-3 sm:w-3.5 sm:h-3.5 {{ $i <= $review->rating ? 'text-teal-400' : 'text-zinc-300' }}" variant="{{ $i <= $review->rating ? 'solid' : 'outline' }}" />
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-0.5 sm:gap-1">
                                    <span class="text-[10px] sm:text-xs text-zinc-400">{{ $review->created_at->format('d M Y, H:i') }}</span>
                                    @if($review->isEdited())
                                        <span class="text-[9px] sm:text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-400 px-1 sm:px-1.5 py-0.5 rounded italic leading-none">{{ __('global.edited') ?? '(disunting)' }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($review->comment)
                                <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed ml-10 sm:ml-12">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 sm:py-16 text-center">
                    <flux:icon.chat-bubble-left-ellipsis class="w-10 h-10 sm:w-12 sm:h-12 text-zinc-200 dark:text-zinc-700 mb-3 sm:mb-4" />
                    <h4 class="text-sm sm:text-base font-bold text-zinc-400 mb-1">{{ __('global.no_reviews') ?? 'Belum ada ulasan' }}</h4>
                    <p class="text-xs sm:text-sm text-zinc-400">{{ __('global.be_the_first_review') ?? 'Jadilah yang pertama mengulas buku ini!' }}</p>
                </div>
            @endif
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="fixed bottom-4 right-4 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 px-5 py-3 rounded-2xl shadow-xl z-50 flex items-center gap-3 text-sm font-semibold" x-data x-init="setTimeout(() => $el.remove(), 3000)">
                <flux:icon.check-circle class="w-5 h-5 text-teal-400" />
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="fixed bottom-4 right-4 bg-red-600 text-white px-5 py-3 rounded-2xl shadow-xl z-50 flex items-center gap-3 text-sm font-semibold" x-data x-init="setTimeout(() => $el.remove(), 4000)">
                <flux:icon.exclamation-circle class="w-5 h-5 text-white" />
                {{ session('error') }}
            </div>
        @endif
    </div>
</div>