<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('categories.title') }}</flux:heading>
        <flux:modal.trigger name="category-form">
            <flux:button wire:click="showCreateForm" variant="primary" icon="plus">
                {{ __('categories.add_new') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    <!-- Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <flux:input 
            wire:model.live.debounce.300ms="search" 
            placeholder="{{ __('categories.search_placeholder') }}" 
            icon="magnifying-glass"
            class="max-w-md"
        />
    </div>

    <!-- Create/Edit Modal -->
    <flux:modal name="category-form" :show="$showCreateForm" class="max-w-lg">
        <form wire:submit.prevent="saveCategory" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ $editingCategory ? __('categories.edit_title') : __('categories.add_new') }}</flux:heading>
            </div>

            <div>
                <flux:label>{{ __('categories.name') }}</flux:label>
                <flux:input wire:model="name" placeholder="{{ __('categories.name_placeholder') }}" class="mt-1" />
                <flux:error name="name" />
            </div>

            <div class="flex gap-3 justify-end">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost" wire:click="cancelForm">{{ __('global.cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">
                    {{ $editingCategory ? __('global.save') : __('categories.add_new') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Success Message -->
    @if(session('success'))
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Categories Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('categories.name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('categories.books_count') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('global.added_at') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('categories.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($categories as $category)
                        <tr wire:key="category-{{ $category->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $category->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <flux:badge variant="outline">
                                    {{ $category->books_count }} {{ __('global.books_count') }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $category->created_at->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <flux:modal.trigger name="category-form">
                                    <flux:button 
                                        wire:click="editCategory({{ $category->id }})" 
                                        size="sm"
                                    >
                                        {{ __('global.edit') }}
                                    </flux:button>
                                </flux:modal.trigger>
                                <flux:button 
                                    size="sm" 
                                    variant="danger" 
                                    wire:click="deleteCategory({{ $category->id }})"
                                    wire:confirm="{{ $category->books_count > 0 ? __('categories.delete_confirm_with_books') : __('categories.delete_confirm') }}"
                                >
                                    {{ __('global.delete') }}
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('categories.no_categories_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $categories->links() }}
        </div>
    </div>
</div>