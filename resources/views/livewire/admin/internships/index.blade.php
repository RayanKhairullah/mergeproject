<div>

<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Internship Management</h1>
            <p class="text-gray-500 mt-1 dark:text-gray-400">Manage interns and mentor assignments</p>
        </div>
        <div class="flex-shrink-0 flex items-center gap-2">
            <flux:button variant="primary" icon="plus" wire:click="create">Assign New Intern</flux:button>
        </div>
    </div>

    <div class="w-full bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-800 overflow-x-auto mt-4">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-800 dark:text-gray-300">
                <tr>
                    <th scope="col" class="px-6 py-3">Intern</th>
                    <th scope="col" class="px-6 py-3">Mentor</th>
                    <th scope="col" class="px-6 py-3">Department/Position</th>
                    <th scope="col" class="px-6 py-3">Period</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Contract</th>
                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($internships as $internship)
                    <tr class="bg-white border-b dark:bg-zinc-900 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            {{ $internship->user->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4">{{ $internship->mentor->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $internship->division->name ?? '-' }}</span><br>
                            <span class="text-xs text-zinc-500">{{ $internship->department }} / {{ $internship->position }}</span>
                        </td>
                        <td class="px-6 py-4">{{ $internship->start_date->format('M d, Y') }} - {{ $internship->end_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            @if($internship->status === 'active')
                                <flux:badge size="sm" color="green">Active</flux:badge>
                            @elseif($internship->status === 'completed')
                                <flux:badge size="sm" color="blue">Completed</flux:badge>
                            @else
                                <flux:badge size="sm" color="red">Terminated</flux:badge>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($internship->contract_path)
                                <a href="{{ Storage::url($internship->contract_path) }}" target="_blank" title="View Contract">
                                    <flux:icon.document-check class="w-5 h-5 text-teal-600" />
                                </a>
                            @else
                                <span class="text-zinc-300">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <flux:button variant="ghost" size="sm" wire:click="edit({{ $internship->id }})">Edit</flux:button>
                            <flux:button variant="danger" size="sm" wire:click="delete({{ $internship->id }})" wire:confirm="Are you sure?">Delete</flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center">No internships found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $internships->links() }}
    </div>

    <!-- Modal -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-hidden">
        <div class="fixed inset-0 bg-gray-900/50 dark:bg-black/50 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>
        <div class="relative w-full max-w-2xl mx-auto flex flex-col max-h-full">
            <div class="relative flex flex-col w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-2xl outline-none focus:outline-none overflow-hidden max-h-[90vh]">
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 shrink-0">
                    <h3 class="text-lg sm:text-xl font-semibold text-zinc-900 dark:text-white">
                        {{ $editingId ? 'Edit Assignment' : 'Assign Intern' }}
                    </h3>
                    <button class="p-1 ml-auto border-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800" wire:click="closeModal">
                        <span class="text-2xl font-semibold leading-none">&times;</span>
                    </button>
                </div>
                <div class="relative p-4 sm:p-6 flex-auto overflow-y-auto custom-scrollbar">
                    <form wire:submit.prevent="save" class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <flux:field>
                            <flux:label>Intern</flux:label>
                            <flux:select wire:model="intern_id" required>
                                <option value="">-- Select Intern --</option>
                                @foreach($interns as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="intern_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Mentor</flux:label>
                            <flux:select wire:model="mentor_id" required>
                                <option value="">-- Select Mentor --</option>
                                @foreach($mentors as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="mentor_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Division</flux:label>
                            <flux:select wire:model="division_id" required>
                                <option value="">-- Select Division --</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="division_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Department</flux:label>
                            <flux:input wire:model="department" required placeholder="e.g. IT, HR, Operation" />
                            <flux:error name="department" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Position</flux:label>
                            <flux:input wire:model="position" required placeholder="e.g. Software Engineer Intern" />
                            <flux:error name="position" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Start Date</flux:label>
                            <flux:input type="date" wire:model="start_date" required />
                            <flux:error name="start_date" />
                        </flux:field>

                        <flux:field>
                            <flux:label>End Date</flux:label>
                            <flux:input type="date" wire:model="end_date" required />
                            <flux:error name="end_date" />
                        </flux:field>

                        <flux:field class="sm:col-span-2">
                            <flux:label>Internship Contract (PDF Only)</flux:label>
                            <flux:input type="file" wire:model="contract" accept=".pdf" />
                            <flux:error name="contract" />
                            
                            @if($contract)
                                <div class="mt-2 p-3 bg-teal-50 dark:bg-teal-900/20 rounded-lg border border-teal-100 dark:border-teal-800 flex items-center justify-between gap-2 text-sm">
                                    <div class="flex items-center gap-2 text-teal-700 dark:text-teal-400">
                                        <flux:icon.document-text class="w-5 h-5" />
                                        <span class="truncate max-w-[200px]">{{ $contract->getClientOriginalName() }}</span>
                                    </div>
                                    <flux:badge color="teal" size="sm">Ready to upload</flux:badge>
                                </div>
                            @endif

                            @if($editingId && $internships->find($editingId)?->contract_path)
                                <div class="mt-2 text-xs text-blue-600">
                                    <a href="{{ Storage::url($internships->find($editingId)->contract_path) }}" target="_blank" class="flex items-center gap-1 font-medium hover:underline">
                                        <flux:icon.document-text class="w-4 h-4" /> View Current Contract
                                    </a>
                                </div>
                            @endif
                        </flux:field>

                        <flux:field class="sm:col-span-2">
                            <flux:label>Status</flux:label>
                            <flux:select wire:model="status" required>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="terminated">Terminated</option>
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>

                        <div class="col-span-1 sm:col-span-2 flex justify-end gap-3 mt-4 pt-5 border-t border-zinc-200 dark:border-zinc-800">
                            <flux:button variant="ghost" wire:click="closeModal">Cancel</flux:button>
                            <flux:button type="submit" variant="primary">Save Assignment</flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
