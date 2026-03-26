<?php

use App\Models\Employee;
use App\Models\OrgSection;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function with(): array
    {
        return [
            // Fetch sections with their employees
            'sections' => OrgSection::with(['employees' => function($q) {
                $q->orderBy('order');
            }, 'employees.division', 'employees.parent', 'employees.children'])->orderBy('order')->get(),
            
            // Unassigned employees (just in case they haven't been assigned yet)
            'unassignedEmployees' => Employee::whereNull('org_section_id')
                ->with(['division', 'parent'])
                ->orderBy('order')
                ->get()
        ];
    }
}; ?>

<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center max-w-2xl mx-auto mb-16">
        <h1 class="text-4xl font-extrabold tracking-tight text-zinc-900 dark:text-white sm:text-5xl">
            Organization Structure
        </h1>
        <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">
            Meet the team leading our divisions and executing our vision.
        </p>
    </div>

    @forelse($sections as $section)
        <div class="mb-16">
            <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white mb-6 text-center">{{ $section->name }}</h2>
            
            @if($section->display_mode === 'tree')
                <div class="w-full p-8 min-h-[400px] overflow-x-auto flex flex-col items-center">
                    @php
                        // For tree mode, we only want the roots of this section
                        $roots = $section->employees->whereNull('parent_id');
                    @endphp
                    @if($roots->isEmpty())
                        <div class="flex flex-col items-center justify-center p-20 text-center">
                            <flux:icon.users class="w-16 h-16 text-zinc-400 mb-4"/>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-white">No Structure Defined</h3>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Our organizational structure is currently being updated. Please check back later.</p>
                        </div>
                    @else
                        <div class="flex flex-row justify-center min-w-max gap-8 pb-10 mt-4">
                            @foreach($roots as $employee)
                                @include('livewire.admin.employees.tree-node', ['employee' => $employee])
                            @endforeach
                        </div>
                    @endif
                </div>

            @elseif($section->display_mode === 'table')
                <div class="w-full glass-card rounded-3xl overflow-hidden min-h-[300px]">
                    @php
                        $tableEmps = $section->employees;
                        $columns = is_array($section->table_columns) ? $section->table_columns : [];
                    @endphp
                    
                    @if($tableEmps->isEmpty())
                        <div class="flex flex-col items-center justify-center p-20 text-center">
                            <flux:icon.table-cells class="w-16 h-16 text-zinc-400 mb-4"/>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-white">Directory Empty</h3>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Our personnel directory is currently being updated.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
                                <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-800/50 dark:text-zinc-300">
                                    <tr>
                                        @if(empty($columns))
                                            <!-- Fallback if no dynamic columns defined -->
                                            <th scope="col" class="px-6 py-4">Employee</th>
                                            <th scope="col" class="px-6 py-4">NIP</th>
                                            <th scope="col" class="px-6 py-4">Position</th>
                                            <th scope="col" class="px-6 py-4">Division</th>
                                        @else
                                            @foreach($columns as $col)
                                                <th scope="col" class="px-6 py-4">{{ $col['header'] ?? 'Column' }}</th>
                                            @endforeach
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700/50">
                                    @foreach($tableEmps as $employee)
                                        <tr class="bg-white/50 dark:bg-zinc-900/50 hover:bg-zinc-50 dark:hover:bg-zinc-800/80 transition-colors">
                                            @if(empty($columns))
                                                <!-- Fallback row rendering -->
                                                <td class="px-6 py-4 flex items-center gap-4">
                                                    <img src="{{ $employee->image_url }}" alt="{{ $employee->name }}" class="w-10 h-10 rounded-full border border-zinc-200 dark:border-zinc-700 object-cover">
                                                    <div>
                                                        <div class="font-bold text-zinc-900 dark:text-white">{{ $employee->name }}</div>
                                                        <div class="text-[10px] text-zinc-500 uppercase tracking-wider mt-0.5">{{ $employee->gender }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 font-mono text-xs">{{ $employee->nip ?? '-' }}</td>
                                                <td class="px-6 py-4">
                                                    <span class="inline-flex items-center rounded-md bg-amber-50 dark:bg-amber-400/10 px-2 py-1 text-xs font-medium text-amber-700 dark:text-amber-400 ring-1 ring-inset ring-amber-600/20">
                                                        {{ $employee->position ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">{{ $employee->division->name ?? '-' }}</td>
                                            @else
                                                @foreach($columns as $col)
                                                    <td class="px-6 py-4">
                                                        @php
                                                            $field = $col['field'] ?? '';
                                                            $value = '-';
                                                            
                                                            // Standard fields
                                                            if ($field === 'name') {
                                                                $value = '<div class="flex items-center gap-4"><img src="'.$employee->image_url.'" alt="'.$employee->name.'" class="w-10 h-10 rounded-full border border-zinc-200 dark:border-zinc-700 object-cover"><div><div class="font-bold text-zinc-900 dark:text-white">'.$employee->name.'</div><div class="text-[10px] text-zinc-500 uppercase tracking-wider mt-0.5">'.$employee->gender.'</div></div></div>';
                                                            } elseif ($field === 'division' || $field === 'division_id') {
                                                                $value = $employee->division->name ?? '-';
                                                            } elseif ($field === 'position') {
                                                                $value = '<span class="inline-flex items-center rounded-md bg-amber-50 dark:bg-amber-400/10 px-2 py-1 text-xs font-medium text-amber-700 dark:text-amber-400 ring-1 ring-inset ring-amber-600/20">'.($employee->position ?? '-').'</span>';
                                                            } elseif (in_array($field, ['nip', 'gender'])) {
                                                                $value = $employee->{$field} ?? '-';
                                                            } else {
                                                                // Custom fields from JSON
                                                                $custom = $employee->custom_fields ?? [];
                                                                $value = $custom[$field] ?? '-';
                                                            }
                                                        @endphp
                                                        {!! $value !!}
                                                    </td>
                                                @endforeach
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @empty
        <div class="w-full glass-card rounded-3xl p-8 text-center text-zinc-500">
            No organizational sections have been defined yet.
        </div>
    @endforelse

    <!-- Unassigned Fallback -->
    @if($unassignedEmployees->isNotEmpty())
        <div class="mt-16 pt-8 border-t border-zinc-200 dark:border-zinc-800">
            <h2 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white mb-6 text-center text-opacity-50">Unassigned Personnel</h2>
             <div class="w-full glass-card rounded-3xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400 opacity-70 hover:opacity-100 transition-opacity">
                        <thead class="text-xs text-zinc-700 uppercase bg-zinc-50/50 dark:bg-zinc-800/30 dark:text-zinc-300">
                            <tr>
                                <th scope="col" class="px-6 py-3">Employee</th>
                                <th scope="col" class="px-6 py-3">Position</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unassignedEmployees as $employee)
                                <tr class="bg-white/30 dark:bg-zinc-900/30 border-t border-zinc-200 dark:border-zinc-800/50">
                                    <td class="px-6 py-3">{{ $employee->name }}</td>
                                    <td class="px-6 py-3">{{ $employee->position ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
             </div>
        </div>
    @endif
</div>
