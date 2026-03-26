<?php

use App\Models\Division;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.admin')] class extends Component {
    use LivewireAlert;
    use WithPagination;

    public $name = '';
    public $editingId = null;
    public $isModalOpen = false;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function create()
    {
        $this->authorize('access dashboard'); // Or specific permission
        $this->reset(['name', 'editingId']);
        $this->isModalOpen = true;
    }

    public function edit(Division $division)
    {
        $this->authorize('access dashboard');
        $this->editingId = $division->id;
        $this->name = $division->name;
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->authorize('access dashboard');
        $this->validate();

        $slug = Str::slug($this->name);
        
        // Ensure slug uniqueness
        $originalSlug = $slug;
        $counter = 1;
        while (Division::where('slug', $slug)->where('id', '!=', $this->editingId)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        if ($this->editingId) {
            Division::find($this->editingId)->update([
                'name' => $this->name,
                'slug' => $slug,
            ]);
            $this->alert('success', 'Division updated successfully.');
        } else {
            Division::create([
                'name' => $this->name,
                'slug' => $slug,
            ]);
            $this->alert('success', 'Division created successfully.');
        }

        $this->closeModal();
    }

    public function delete(Division $division)
    {
        $this->authorize('access dashboard');
        $division->delete();
        $this->alert('success', 'Division deleted successfully.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['name', 'editingId']);
    }

    public function with(): array
    {
        return [
            'divisions' => Division::latest()->paginate(10)
        ];
    }
}; ?>

<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
        <div>
            <div class="mb-2">
                <flux:button variant="subtle" size="sm" icon="arrow-left" href="{{ route('admin.employees.index') }}">Back to Employees</flux:button>
            </div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Manage Divisions</h1>
        </div>
        <flux:button variant="primary" wire:click="create">Add Division</flux:button>
    </div>

    <div class="w-full bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-800 overflow-hidden">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-800 dark:text-gray-300">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Name</th>
                    <th scope="col" class="px-6 py-3">Slug</th>
                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($divisions as $division)
                    <tr class="bg-white border-b dark:bg-zinc-900 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800">
                        <td class="px-6 py-4">{{ $division->id }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $division->name }}</td>
                        <td class="px-6 py-4">{{ $division->slug }}</td>
                        <td class="px-6 py-4 text-right">
                            <flux:button variant="ghost" size="sm" wire:click="edit({{ $division->id }})">Edit</flux:button>
                            <flux:button variant="danger" size="sm" wire:click="delete({{ $division->id }})" wire:confirm="Are you sure you want to delete this division?">Delete</flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center">No divisions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $divisions->links() }}
    </div>

    <!-- Modal for Create/Edit -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
        <div class="fixed inset-0 bg-gray-900/50 dark:bg-black/50 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>
        <div class="relative w-full max-w-md mx-auto my-6">
            <div class="relative flex flex-col w-full bg-white dark:bg-zinc-900 border-0 rounded-xl shadow-lg outline-none focus:outline-none">
                <div class="flex items-start justify-between p-5 border-b border-solid rounded-t dark:border-zinc-800">
                    <h3 class="text-xl font-semibold dark:text-white">
                        {{ $editingId ? 'Edit Division' : 'Add Division' }}
                    </h3>
                    <button class="p-1 ml-auto border-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" wire:click="closeModal">
                        <span class="text-2xl font-semibold leading-none">&times;</span>
                    </button>
                </div>
                <div class="relative p-6 flex-auto">
                    <form wire:submit.prevent="save">
                        <div class="mb-4">
                            <flux:field>
                                <flux:label>Name</flux:label>
                                <flux:input wire:model="name" placeholder="e.g. IT, HR, Finance" required />
                                <flux:error name="name" />
                            </flux:field>
                        </div>
                        <div class="flex justify-end gap-2 mt-6">
                            <flux:button variant="ghost" wire:click="closeModal">Cancel</flux:button>
                            <flux:button type="submit" variant="primary">Save</flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
