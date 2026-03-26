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
