<?php

use App\Models\Employee;
use App\Models\Division;
use App\Models\User;
use App\Models\OrgSection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.admin')] class extends Component {
    use LivewireAlert;
    use WithPagination;
    use Livewire\WithFileUploads;

    // View State Setup
    public $activeTab = 'all'; // 'all' or org_section_id
    
    // Form State Setup
    public $isModalOpen = false;
    public $editingId = null;
    
    // Model attributes
    public $parent_id = null;
    public $division_id = null;
    public $user_id = null;
    public $name = '';
    public $nip = '';
    public $gender = 'male';
    public $position = '';
    public $order = 0;
    public $org_section_id = null;
    public $custom_fields = [];
    public $image = null;
    public $existing_image = null;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:255|unique:employees,nip,' . ($this->editingId ?? 'NULL'),
            'gender' => 'required|in:male,female',
            'position' => 'nullable|string|max:255',
            'order' => 'required|integer',
            'parent_id' => 'nullable|exists:employees,id',
            'division_id' => 'nullable|exists:divisions,id',
            'user_id' => 'nullable|exists:users,id',
            'org_section_id' => 'nullable|exists:org_sections,id',
            'custom_fields' => 'nullable|array',
            'image' => 'nullable|image|max:2048', // 2MB Max
        ];
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function create()
    {
        $this->authorize('access dashboard');
        $this->reset(['editingId', 'name', 'nip', 'gender', 'position', 'order', 'parent_id', 'division_id', 'user_id', 'org_section_id', 'custom_fields', 'image', 'existing_image']);
        $this->isModalOpen = true;
    }

    public function edit(Employee $employee)
    {
        $this->authorize('access dashboard');
        $this->reset(['image']);
        $this->editingId = $employee->id;
        $this->name = $employee->name;
        $this->nip = $employee->nip;
        $this->gender = $employee->gender;
        $this->position = $employee->position;
        $this->order = $employee->order;
        $this->parent_id = $employee->parent_id;
        $this->division_id = $employee->division_id;
        $this->user_id = $employee->user_id;
        $this->org_section_id = $employee->org_section_id;
        $this->custom_fields = $employee->custom_fields ?? [];
        $this->existing_image = $employee->image;

        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->authorize('access dashboard');
        $this->validate();

        $data = [
            'name' => $this->name,
            'nip' => $this->nip,
            'gender' => $this->gender,
            'position' => $this->position,
            'order' => $this->order,
            'parent_id' => $this->parent_id ?: null,
            'division_id' => $this->division_id ?: null,
            'user_id' => $this->user_id ?: null,
            'org_section_id' => $this->org_section_id ?: null,
            'custom_fields' => $this->custom_fields,
        ];

        // Handle Image Upload with WebP conversion
        if ($this->image) {
            $filename = 'employees/' . uniqid() . '.webp';
            
            // Create image manager with desired driver
            $manager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );

            // Read image from temporary upload
            $img = $manager->read($this->image->getRealPath());

            // Convert to webp and save to public storage
            $encoded = $img->toWebp(85);
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, (string)$encoded);
            
            $data['image'] = $filename;

            // Delete old image if exists
            if ($this->editingId) {
                $oldEmployee = Employee::find($this->editingId);
                if ($oldEmployee->image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldEmployee->image);
                }
            }
        }

        // Handle Automatic Order Shifting
        if ($this->org_section_id) {
            $conflict = Employee::where('org_section_id', $this->org_section_id)
                ->where('order', $this->order)
                ->when($this->editingId, fn($q) => $q->where('id', '!=', $this->editingId))
                ->exists();

            if ($conflict) {
                Employee::where('org_section_id', $this->org_section_id)
                    ->where('order', '>=', $this->order)
                    ->when($this->editingId, fn($q) => $q->where('id', '!=', $this->editingId))
                    ->increment('order');
            }
        }

        if ($this->editingId) {
            Employee::find($this->editingId)->update($data);
            $this->alert('success', 'Employee updated successfully.');
        } else {
            Employee::create($data);
            $this->alert('success', 'Employee created successfully.');
        }

        $this->closeModal();
    }

    public function delete(Employee $employee)
    {
        $this->authorize('access dashboard');
        
        // Ensure children are detached or deleted depending on business logic
        // We set nullOnDelete in migration, so it's safe to just delete
        $employee->delete();
        $this->alert('success', 'Employee deleted successfully.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function with(): array
    {
        $empQuery = Employee::with(['division', 'parent', 'orgSection'])->orderBy('order');
        if ($this->activeTab !== 'all') {
            $empQuery->where('org_section_id', (int)$this->activeTab);
        }

        $rootQuery = Employee::whereNull('parent_id')->with(['division', 'children', 'orgSection'])->orderBy('order');
        if ($this->activeTab !== 'all') {
            $rootQuery->where('org_section_id', (int)$this->activeTab);
        }

        return [
            'displayEmployees' => $empQuery->paginate(10),
            'displayRoots' => $rootQuery->get(),
            'divisionsList' => Division::all(),
            'usersList' => User::all(),
            'orgSectionsList' => OrgSection::orderBy('order')->get(),
            'allEmployeesList' => Employee::orderBy('name')->get()
        ];
    }
}; ?>

<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Manage Structure</h1>
            <p class="text-gray-500 mt-1 dark:text-gray-400">Manage employees and organizational hierarchy</p>
        </div>
        <div class="flex-shrink-0 flex items-center gap-2">
            <flux:button variant="subtle" icon="building-office-2" href="{{ route('admin.divisions.index') }}" class="hidden sm:flex">Divisions</flux:button>
            <flux:button variant="subtle" icon="rectangle-group" href="{{ route('admin.org-sections.index') }}" class="hidden sm:flex">Types</flux:button>
            <flux:dropdown class="sm:hidden">
                <flux:button variant="subtle" icon="ellipsis-vertical" />
                <flux:menu>
                    <flux:menu.item icon="building-office-2" href="{{ route('admin.divisions.index') }}">Manage Divisions</flux:menu.item>
                    <flux:menu.item icon="rectangle-group" href="{{ route('admin.org-sections.index') }}">Manage Types</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
            <flux:button variant="primary" icon="plus" wire:click="create">Add Member</flux:button>
        </div>
    </div>

    <div class="w-full flex p-1 bg-gray-100 dark:bg-zinc-800 rounded-lg overflow-x-auto mb-4 space-x-1 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
        <button wire:click="setActiveTab('all')" class="flex-shrink-0 whitespace-nowrap px-4 py-1.5 text-sm rounded-md transition-colors {{ $activeTab === 'all' ? 'bg-white dark:bg-zinc-700 shadow shadow-sm text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
            All Records
        </button>
        @foreach($orgSectionsList as $sec)
        <button wire:click="setActiveTab('{{ $sec->id }}')" class="flex-shrink-0 whitespace-nowrap px-4 py-1.5 text-sm rounded-md transition-colors {{ (string)$activeTab === (string)$sec->id ? 'bg-white dark:bg-zinc-700 shadow shadow-sm text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
            {{ $sec->name }}
        </button>
        @endforeach
    </div>

    @php
        $activeSection = $activeTab !== 'all' ? $orgSectionsList->firstWhere('id', (int)$activeTab) : null;
        $currentMode = $activeSection ? $activeSection->display_mode : 'table';
    @endphp

    @if($currentMode === 'table')
        <div class="w-full bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-800 overflow-x-auto mt-4">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-800 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">Member</th>
                        <th scope="col" class="px-6 py-3">NIP</th>
                        <th scope="col" class="px-6 py-3">Type/Section</th>
                        <th scope="col" class="px-6 py-3">Position</th>
                        <th scope="col" class="px-6 py-3">Division</th>
                        <th scope="col" class="px-6 py-3">Reports To</th>
                        <th scope="col" class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($displayEmployees as $employee)
                        <tr class="bg-white border-b dark:bg-zinc-900 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <img src="{{ $employee->image_url }}" alt="{{ $employee->name }}" class="w-8 h-8 rounded-full border border-gray-200 dark:border-zinc-700">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $employee->name }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $employee->nip ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($employee->orgSection)
                                    <flux:badge size="sm" color="zinc">{{ $employee->orgSection->name }} - {{ $employee->orgSection->display_mode === 'tree' ? 'Hierarchy Tree' : 'Tabel' }}</flux:badge>
                                @else
                                    <span class="text-gray-400 italic">None</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $employee->position ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $employee->division->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $employee->parent->name ?? 'Root (Top Level)' }}</td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <flux:button variant="ghost" size="sm" wire:click="edit({{ $employee->id }})">Edit</flux:button>
                                <flux:button variant="danger" size="sm" wire:click="delete({{ $employee->id }})" wire:confirm="Are you sure you want to delete this member?">Delete</flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center">No members found in the organization.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $displayEmployees->links() }}
        </div>
    @else
        <!-- Hierarchy Tree View -->
        <div class="w-full bg-white dark:bg-zinc-900 rounded-xl shadow-inner border border-zinc-200 dark:border-zinc-800 p-8 min-h-[600px] overflow-auto flex justify-center items-start mt-4">
            <div class="flex flex-row justify-center min-w-max gap-8">
                @foreach($displayRoots as $root)
                    @include('livewire.admin.employees.tree-node', ['employee' => $root])
                @endforeach
            </div>
            @if($displayRoots->isEmpty())
                <div class="text-gray-500 text-center flex flex-col items-center justify-center pt-20">
                    <flux:icon.users class="w-16 h-16 mb-4 opacity-50"/>
                    <p>Hierarchy is empty. Add a root member for this section to get started.</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Create/Edit Modal -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-hidden">
        <div class="fixed inset-0 bg-gray-900/50 dark:bg-black/50 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>
        <div class="relative w-full max-w-2xl mx-auto flex flex-col max-h-full">
            <div class="relative flex flex-col w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-2xl outline-none focus:outline-none overflow-hidden max-h-[90vh]">
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 shrink-0">
                    <h3 class="text-lg sm:text-xl font-semibold text-zinc-900 dark:text-white">
                        {{ $editingId ? 'Edit Member' : 'Add Member' }}
                    </h3>
                    <button class="p-1 ml-auto border-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800" wire:click="closeModal">
                        <span class="text-2xl font-semibold leading-none">&times;</span>
                    </button>
                </div>
                <div class="relative p-4 sm:p-6 flex-auto overflow-y-auto custom-scrollbar">
                    <form wire:submit.prevent="save" class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <flux:field>
                            <flux:label>Full Name</flux:label>
                            <flux:input wire:model="name" required />
                            <flux:error name="name" />
                        </flux:field>

                        <flux:field>
                            <flux:label>NIP <span class="text-gray-400 font-normal text-xs ml-1">(Optional)</span></flux:label>
                            <flux:input wire:model="nip" />
                            <flux:error name="nip" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Position <span class="text-gray-400 font-normal text-xs ml-1">(Optional)</span></flux:label>
                            <flux:input wire:model="position" placeholder="e.g. Director, Manager" />
                            <flux:error name="position" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Gender</flux:label>
                            <flux:select wire:model="gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </flux:select>
                            <flux:error name="gender" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Division <span class="text-gray-400 font-normal text-xs ml-1">(Optional)</span></flux:label>
                            <flux:select wire:model="division_id">
                                <option value="">-- No Division --</option>
                                @foreach($divisionsList as $div)
                                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="division_id" />
                        </flux:field>

                        <flux:field class="sm:col-span-2">
                            <flux:label>Profile Image</flux:label>
                            <div class="flex items-center gap-4 mt-2">
                                @if($image)
                                    <img src="{{ $image->temporaryUrl() }}" class="w-16 h-16 rounded-full object-cover border-2 border-primary-500">
                                @elseif($existing_image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($existing_image) }}" class="w-16 h-16 rounded-full object-cover border">
                                @else
                                    <div class="w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center border border-dashed border-zinc-300 dark:border-zinc-700">
                                        <flux:icon.user class="w-8 h-8 text-zinc-400"/>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <input type="file" wire:model="image" class="text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-zinc-50 file:text-zinc-700 hover:file:bg-zinc-100 dark:file:bg-zinc-800 dark:file:text-zinc-300">
                                    <p class="mt-1 text-xs text-zinc-500">PNG, JPG, WEBP up to 2MB. Auto-converted to WebP.</p>
                                </div>
                            </div>
                            <flux:error name="image" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Employee Type <span class="text-gray-400 font-normal text-xs ml-1">(Optional)</span></flux:label>
                            <flux:select wire:model.live="org_section_id">
                                <option value="">-- No Section --</option>
                                @foreach($orgSectionsList as $sec)
                                    <option value="{{ $sec->id }}">{{ $sec->name }} - {{ $sec->display_mode === 'tree' ? 'Hierarchy Tree' : 'Tabel' }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="org_section_id" />
                        </flux:field>

                        @php
                            $selectedSection = $org_section_id ? $orgSectionsList->firstWhere('id', (int)$org_section_id) : null;
                        @endphp
                        
                        @if(!$selectedSection || $selectedSection->display_mode === 'tree')
                            <flux:field>
                                <flux:label>Reports To (Parent) <span class="text-gray-400 font-normal text-xs ml-1">(Optional)</span></flux:label>
                                <flux:select wire:model="parent_id">
                                    <option value="">-- Top Level (Root) --</option>
                                    @foreach($allEmployeesList as $emp)
                                        @if($emp->id !== $editingId && (!$org_section_id || (string)$emp->org_section_id === (string)$org_section_id))
                                            <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->position }})</option>
                                        @endif
                                    @endforeach
                                </flux:select>
                                <flux:error name="parent_id" />
                            </flux:field>
                        @endif

                        <flux:field>
                            <flux:label>Linked User Account <span class="text-gray-400 font-normal text-xs ml-1">(Optional)</span></flux:label>
                            <flux:select wire:model="user_id">
                                <option value="">-- No Linked Account --</option>
                                @foreach($usersList as $usr)
                                    <option value="{{ $usr->id }}">{{ $usr->name }} ({{ $usr->email }})</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="user_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Display Order</flux:label>
                            <flux:input type="number" wire:model="order" required />
                            <flux:error name="order" />
                            @if($org_section_id)
                                @php
                                    $usedOrders = \App\Models\Employee::where('org_section_id', $org_section_id)
                                        ->when($editingId, fn($q) => $q->where('id', '!=', $editingId))
                                        ->orderBy('order')
                                        ->get(['order', 'name']);
                                @endphp
                                @if($usedOrders->isNotEmpty())
                                    <div class="mt-2 p-3 bg-zinc-50 dark:bg-zinc-800 rounded-md border border-zinc-200 dark:border-zinc-700">
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Urutan order yang sudah terpakai di tipe ini:</span>
                                        <ul class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-h-20 overflow-y-auto space-y-1">
                                        @foreach($usedOrders as $uo)
                                            <li><span class="font-medium">Order {{ $uo->order }}:</span> {{ $uo->name }}</li>
                                        @endforeach
                                        </ul>
                                        <div class="mt-2 text-[10px] text-amber-600 dark:text-amber-400 font-medium">
                                            * Jika Anda menggunakan urutan (order) yang sama, data yang sudah ada di urutan tersebut (dan seterusnya) akan otomatis digeser ukurannya ke bawah (+1).
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </flux:field>

                        @if($org_section_id)
                            @php
                                $standardFields = ['name', 'nip', 'position', 'gender', 'division_id', 'parent_id', 'user_id', 'order', 'show_in_tree', 'show_in_table', 'org_section_id'];
                            @endphp
                            @if($selectedSection && $selectedSection->display_mode === 'table' && is_array($selectedSection->table_columns))
                                <div class="col-span-1 md:col-span-2 mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                                    <h4 class="text-sm font-semibold dark:text-zinc-200 mb-2 mt-2">Custom Fields for {{ $selectedSection->name }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($selectedSection->table_columns as $col)
                                        @if(isset($col['field']) && !in_array($col['field'], $standardFields))
                                            <flux:field>
                                                <flux:label>{{ $col['header'] ?? $col['field'] }}</flux:label>
                                                <flux:input wire:model="custom_fields.{{ $col['field'] }}" />
                                            </flux:field>
                                        @endif
                                    @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="col-span-1 sm:col-span-2 flex flex-col sm:flex-row justify-end gap-3 mt-4 pt-5 border-t border-zinc-200 dark:border-zinc-800">
                            <flux:button variant="ghost" wire:click="closeModal" class="w-full sm:w-auto order-2 sm:order-1">Cancel</flux:button>
                            <flux:button type="submit" variant="primary" class="w-full sm:w-auto order-1 sm:order-2">Save Member</flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
