<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Edit Book</flux:heading>
            <flux:button href="{{ route('admin.books.index') }}" variant="ghost" icon="arrow-left">
                Back to Books
            </flux:button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div>
                        <flux:label for="title">Title *</flux:label>
                        <flux:input 
                            id="title" 
                            name="title" 
                            value="{{ old('title', $book->title) }}" 
                            required 
                            class="mt-1"
                        />
                        <flux:error name="title" />
                    </div>

                    <!-- Author -->
                    <div>
                        <flux:label for="author">Author *</flux:label>
                        <flux:input 
                            id="author" 
                            name="author" 
                            value="{{ old('author', $book->author) }}" 
                            required 
                            class="mt-1"
                        />
                        <flux:error name="author" />
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <flux:label for="description">Description</flux:label>
                    <flux:textarea 
                        id="description" 
                        name="description" 
                        rows="4" 
                        class="mt-1"
                    >{{ old('description', $book->description) }}</flux:textarea>
                    <flux:error name="description" />
                </div>

                <!-- Category -->
                <div>
                    <flux:label for="category_id">Category</flux:label>
                    <flux:select id="category_id" name="category_id" class="mt-1" value="{{ old('category_id', $book->category_id) }}">
                        <flux:select.option value="">Select a category</flux:select.option>
                        @foreach($categories as $category)
                            <flux:select.option value="{{ $category->id }}">
                                {{ $category->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="category_id" />
                </div>

                <!-- Current Files Display -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Current Cover Image -->
                    <div>
                        <flux:label>Current Cover Image</flux:label>
                        @if($book->cover_image)
                            <div class="mt-2">
                                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-32 h-40 object-cover rounded-lg border">
                                <p class="mt-1 text-sm text-gray-500">Current cover image</p>
                            </div>
                        @else
                            <p class="mt-2 text-sm text-gray-500">No cover image uploaded</p>
                        @endif
                    </div>

                    <!-- Current Book File -->
                    <div>
                        <flux:label>Current Book File</flux:label>
                        @if($book->file_path)
                            <div class="mt-2">
                                <div class="flex items-center space-x-2">
                                    <flux:icon name="document-text" class="w-8 h-8 text-red-600" />
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">PDF file uploaded</p>
                                        <p class="text-sm text-gray-500">{{ basename($book->file_path) }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="mt-2 text-sm text-gray-500">No book file uploaded</p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- New Cover Image -->
                    <div>
                        <flux:label for="cover_image">New Cover Image</flux:label>
                        <input 
                            type="file" 
                            id="cover_image" 
                            name="cover_image" 
                            accept="image/jpeg,image/png,image/webp"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-mint-50 file:text-mint-700 hover:file:bg-mint-100"
                        />
                        <p class="mt-1 text-sm text-gray-500">JPEG, PNG, or WebP. Max 2MB. Leave empty to keep current.</p>
                        <flux:error name="cover_image" />
                    </div>

                    <!-- New Book File -->
                    <div>
                        <flux:label for="book_file">New Book File (PDF)</flux:label>
                        <input 
                            type="file" 
                            id="book_file" 
                            name="book_file" 
                            accept="application/pdf"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-mint-50 file:text-mint-700 hover:file:bg-mint-100"
                        />
                        <p class="mt-1 text-sm text-gray-500">PDF only. Max 10MB. Leave empty to keep current.</p>
                        <flux:error name="book_file" />
                    </div>
                </div>

                <!-- Book Stats -->
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Book Statistics</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Downloads:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100 ml-1">{{ number_format($book->download_count) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Reviews:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100 ml-1">{{ $book->reviews->count() }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Average Rating:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100 ml-1">
                                @if($book->reviews->count() > 0)
                                    {{ number_format($book->average_rating, 1) }}/5
                                @else
                                    No ratings yet
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <flux:button href="{{ route('admin.books.index') }}" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        Update Book
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>