<?php

use App\Models\OrgSection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.admin')] class extends Component {
    use LivewireAlert;
    use WithPagination;

    public $isModalOpen = false;
    public $editingId = null;

    public $name = '';
    public $display_mode = 'table';
    public $order = 0;
    
    // table_columns is an array of [header, field]
    public $table_columns = [];

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'display_mode' => 'required|in:tree,table',
            'order' => 'required|integer',
            'table_columns' => 'nullable|array',
            'table_columns.*.header' => 'required|string',
            'table_columns.*.field' => 'required|string',
        ];
    }

    public function addColumn()
    {
        $this->table_columns[] = ['header' => '', 'field' => ''];
    }

    public function removeColumn($index)
    {
        unset($this->table_columns[$index]);
        $this->table_columns = array_values($this->table_columns);
    }

    public function create()
    {
        $this->authorize('access dashboard');
        $this->reset(['editingId', 'name', 'display_mode', 'order', 'table_columns']);
        $this->display_mode = 'table';
        $this->order = 0;
        $this->table_columns = [];
        $this->isModalOpen = true;
    }

    public function edit(OrgSection $section)
    {
        $this->authorize('access dashboard');
        $this->editingId = $section->id;
        $this->name = $section->name;
        $this->display_mode = $section->display_mode;
        $this->order = $section->order;
        $this->table_columns = $section->table_columns ?? [];
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->authorize('access dashboard');
        $this->validate();

        $data = [
            'name' => $this->name,
            'display_mode' => $this->display_mode,
            'order' => $this->order,
            'table_columns' => $this->display_mode === 'table' ? $this->table_columns : null,
        ];

        if ($this->editingId) {
            OrgSection::find($this->editingId)->update($data);
            $this->alert('success', 'Employee Type updated successfully.');
        } else {
            OrgSection::create($data);
            $this->alert('success', 'Employee Type created successfully.');
        }

        $this->closeModal();
    }

    public function delete(OrgSection $section)
    {
        $this->authorize('access dashboard');
        $section->delete();
        $this->alert('success', 'Employee Type deleted successfully.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function with(): array
    {
        return [
            'sections' => OrgSection::orderBy('order')->paginate(10)
        ];
    }
}; ?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="mb-2">
                <flux:button variant="subtle" size="sm" icon="arrow-left" href="{{ route('admin.employees.index') }}">Back to Employees</flux:button>
            </div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Employee Types / Sections</h1>
            <p class="text-gray-500 mt-1 dark:text-gray-400">Manage organization sections and dynamic table columns</p>
        </div>
        <flux:button variant="primary" wire:click="create">Create Type</flux:button>
    </div>

    <div class="w-full bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-800 overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-800 dark:text-gray-300">
                <tr>
                    <th scope="col" class="px-6 py-3">Order</th>
                    <th scope="col" class="px-6 py-3">Type Name</th>
                    <th scope="col" class="px-6 py-3">Display Mode</th>
                    <th scope="col" class="px-6 py-3">Columns Setup</th>
                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sections as $section)
                    <tr class="bg-white border-b dark:bg-zinc-900 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800">
                        <td class="px-6 py-4">{{ $section->order }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $section->name }}</td>
                        <td class="px-6 py-4">
                            <flux:badge color="{{ $section->display_mode === 'tree' ? 'green' : 'blue' }}" inset="top bottom">{{ ucfirst($section->display_mode) }}</flux:badge>
                        </td>
                        <td class="px-6 py-4">
                            @if($section->display_mode === 'table' && $section->table_columns)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($section->table_columns as $col)
                                        <flux:badge size="sm" color="zinc" inset="top bottom">{{ $col['header'] ?? '' }}</flux:badge>
                                    @endforeach
                                </div>
                            @elseif($section->display_mode === 'tree')
                                <span class="text-gray-400 italic">Not applicable for Tree mode</span>
                            @else
                                <span class="text-gray-400 italic">No columns defined</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <flux:button variant="subtle" size="sm" wire:click="edit({{ $section->id }})">Edit</flux:button>
                            <flux:button variant="danger" size="sm" wire:click="delete({{ $section->id }})" wire:confirm="Are you sure you want to delete this type?">Delete</flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center">No employee types defined.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sections->links() }}
    </div>

    <!-- Create/Edit Modal -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
        <div class="fixed inset-0 bg-gray-900/50 dark:bg-black/50 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>
        <div class="relative w-full max-w-3xl mx-auto my-6">
            <div class="relative flex flex-col w-full bg-white dark:bg-zinc-900 border-0 rounded-xl shadow-lg outline-none focus:outline-none overflow-hidden">
                <div class="flex items-start justify-between p-5 border-b border-solid rounded-t dark:border-zinc-800">
                    <h3 class="text-xl font-semibold dark:text-white">
                        {{ $editingId ? 'Edit Type' : 'Create Type' }}
                    </h3>
                    <button class="p-1 ml-auto border-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" wire:click="closeModal">
                        <span class="text-2xl font-semibold leading-none">&times;</span>
                    </button>
                </div>
                <div class="relative p-6 flex-auto max-h-[70vh] overflow-y-auto">
                    <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Type Name (Group Name)</flux:label>
                                <flux:input wire:model="name" placeholder="e.g. Pegawai Organik" required />
                                <flux:error name="name" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Display Order</flux:label>
                                <flux:input type="number" wire:model="order" required />
                                <flux:error name="order" />
                            </flux:field>

                            <flux:field class="md:col-span-2">
                                <flux:label>Display Mode</flux:label>
                                <flux:select wire:model.live="display_mode" required>
                                    <option value="table">Table View</option>
                                    <option value="tree">Hierarchy Tree View</option>
                                </flux:select>
                                <flux:error name="display_mode" />
                            </flux:field>
                        </div>

                        @if($display_mode === 'table')
                            <div class="mt-4 p-4 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700">
                                <div class="flex justify-between items-center mb-4">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white">Dynamic Table Columns</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Define the column headings and data fields. Standard fields: <code>name</code>, <code>nip</code>, <code>position</code>, <code>gender</code>. Any other field name will extract from <code>custom_fields</code>.</p>
                                    </div>
                                    <flux:button size="sm" wire:click="addColumn" icon="plus">Add Column</flux:button>
                                </div>

                                <div class="space-y-3">
                                    @foreach($table_columns as $index => $column)
                                        <div class="flex items-start gap-3 bg-white dark:bg-zinc-900 p-3 rounded-md border border-zinc-200 dark:border-zinc-700" wire:key="col-{{ $index }}">
                                            <div class="flex-1">
                                                <flux:field>
                                                    <flux:label>Table Header</flux:label>
                                                    <flux:input wire:model="table_columns.{{ $index }}.header" placeholder="e.g. Nama Pegawai" required />
                                                    <flux:error name="table_columns.{{ $index }}.header" />
                                                </flux:field>
                                            </div>
                                            <div class="flex-1">
                                                <flux:field>
                                                    <flux:label>Data Field</flux:label>
                                                    <flux:input wire:model="table_columns.{{ $index }}.field" placeholder="e.g. name" required />
                                                    <flux:error name="table_columns.{{ $index }}.field" />
                                                </flux:field>
                                            </div>
                                            <div class="pt-7">
                                                <flux:button variant="danger" size="sm" icon="trash" wire:click="removeColumn({{ $index }})"></flux:button>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if(count($table_columns) === 0)
                                        <div class="text-center py-4 text-gray-500 text-sm italic">
                                            No custom columns added yet. Click "Add Column" above.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="mt-4 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800/30 text-sm">
                                <flux:icon.information-circle class="size-5 inline -mt-0.5 mr-1" />
                                Tree view has a fixed layout mapping parent/child employees. Dynamic table headings are ignored.
                            </div>
                        @endif

                        <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            <flux:button variant="ghost" wire:click="closeModal">Cancel</flux:button>
                            <flux:button type="submit" variant="primary">Save Type</flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
