<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ __('books.add_new') }}</flux:heading>
            <flux:button href="{{ route('admin.books.index') }}" variant="ghost" icon="arrow-left">
                {{ __('books.back_to_books') }}
            </flux:button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div>
                        <flux:label for="title">{{ __('books.book_title') }} *</flux:label>
                        <flux:input 
                            id="title" 
                            name="title" 
                            value="{{ old('title') }}" 
                            required 
                            class="mt-1"
                        />
                        <flux:error name="title" />
                    </div>

                    <!-- Author -->
                    <div>
                        <flux:label for="author">{{ __('books.author') }} *</flux:label>
                        <flux:input 
                            id="author" 
                            name="author" 
                            value="{{ old('author') }}" 
                            required 
                            class="mt-1"
                        />
                        <flux:error name="author" />
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <flux:label for="description">{{ __('books.description') }}</flux:label>
                    <flux:textarea 
                        id="description" 
                        name="description" 
                        rows="4" 
                        class="mt-1"
                    >{{ old('description') }}</flux:textarea>
                    <flux:error name="description" />
                </div>

                <!-- Category -->
                <div>
                    <flux:label for="category_id">{{ __('books.category') }}</flux:label>
                    <flux:select id="category_id" name="category_id" class="mt-1" value="{{ old('category_id') }}">
                        <flux:select.option value="">{{ __('books.select_category') }}</flux:select.option>
                        @foreach($categories as $category)
                            <flux:select.option value="{{ $category->id }}">
                                {{ $category->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="category_id" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Cover Image -->
                    <div x-data="{ photoPreview: null }">
                        <flux:label for="cover_image">{{ __('books.cover_image') }}</flux:label>
                        <input 
                            type="file" 
                            id="cover_image" 
                            name="cover_image" 
                            accept="image/jpeg,image/png,image/webp"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-mint-50 file:text-mint-700 hover:file:bg-mint-100"
                            x-on:change="
                                const file = $event.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => { photoPreview = e.target.result; };
                                    reader.readAsDataURL(file);
                                }
                            "
                        />
                        <template x-if="photoPreview">
                            <div class="mt-3">
                                <p class="text-sm text-gray-500 mb-2">{{ __('books.preview_image') }}:</p>
                                <img :src="photoPreview" class="w-32 h-40 object-cover rounded-lg border shadow-sm">
                            </div>
                        </template>
                        <p class="mt-1 text-sm text-gray-500">{{ __('books.image_requirements') }}</p>
                        <flux:error name="cover_image" />
                    </div>

                    <!-- Book File -->
                    <div>
                        <flux:label for="book_file">{{ __('books.book_file') }}</flux:label>
                        <input 
                            type="file" 
                            id="book_file" 
                            name="book_file" 
                            accept="application/pdf"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-mint-50 file:text-mint-700 hover:file:bg-mint-100"
                        />
                        <p class="mt-1 text-sm text-gray-500">{{ __('books.file_requirements') }}</p>
                        <flux:error name="book_file" />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <flux:button href="{{ route('admin.books.index') }}" variant="ghost">
                        {{ __('global.cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('books.add_new') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>