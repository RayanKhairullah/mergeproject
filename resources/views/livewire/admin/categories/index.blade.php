<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Manage Categories</flux:heading>
        <flux:modal.trigger name="category-form">
            <flux:button wire:click="showCreateForm" variant="primary" icon="plus">
                Add New Category
            </flux:button>
        </flux:modal.trigger>
    </div>

    <!-- Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <flux:input 
            wire:model.live.debounce.300ms="search" 
            placeholder="Search categories..." 
            icon="magnifying-glass"
            class="max-w-md"
        />
    </div>

    <!-- Create/Edit Modal -->
    <flux:modal name="category-form" :show="$showCreateForm" class="max-w-lg">
        <form wire:submit.prevent="saveCategory" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ $editingCategory ? 'Edit Category' : 'Create New Category' }}</flux:heading>
            </div>

            <div>
                <flux:label>Category Name</flux:label>
                <flux:input wire:model="name" placeholder="Enter category name" class="mt-1" />
                <flux:error name="name" />
            </div>

            <div class="flex gap-3 justify-end">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost" wire:click="cancelForm">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">
                    {{ $editingCategory ? 'Update Category' : 'Create Category' }}
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Books Count</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
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
                                    {{ $category->books_count }} {{ Str::plural('book', $category->books_count) }}
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
                                        Edit
                                    </flux:button>
                                </flux:modal.trigger>
                                <flux:button 
                                    size="sm" 
                                    variant="danger" 
                                    wire:click="deleteCategory({{ $category->id }})"
                                    wire:confirm="Are you sure you want to delete this category?{{ $category->books_count > 0 ? ' Books in this category will be uncategorized.' : '' }}"
                                >
                                    Delete
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No categories found
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