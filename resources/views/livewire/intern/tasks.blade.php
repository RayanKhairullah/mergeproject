<div>
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tighter">Tasks Kanban</h1>
            <p class="text-zinc-500 dark:text-zinc-400 mt-1">Drag and drop cards to update status.</p>
        </div>
        <div class="flex items-center gap-3">
            <flux:button variant="ghost" href="{{ route('intern.dashboard') }}" icon="home">Dashboard</flux:button>
            <div class="h-8 w-px bg-zinc-200 dark:bg-zinc-700 hidden md:block"></div>
            <div class="flex flex-col items-end">
                <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Intern Board</span>
                <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </div>

    @if(!$internship)
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 p-6 rounded-2xl flex flex-col items-center text-center">
            <flux:icon.exclamation-triangle class="w-12 h-12 text-amber-500 mb-4" />
            <h3 class="text-lg font-semibold text-amber-900 dark:text-amber-200">No Active Invitation</h3>
            <p class="text-amber-700 dark:text-amber-400 max-w-md mt-1">You do not have an active internship assignment. Please contact HR.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data>
            
            <!-- To-Do Column -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between px-2">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-zinc-400"></div>
                        <h3 class="font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-widest text-xs">To-Do</h3>
                        <span class="text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-500 px-1.5 py-0.5 rounded-md">{{ $todoTasks->count() }}</span>
                    </div>
                </div>
                
                <div id="column-todo" data-status="todo" class="bg-zinc-50/50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 min-h-[600px] flex flex-col gap-4">
                    @forelse($todoTasks as $task)
                        <div wire:key="task-{{ $task->id }}" data-id="{{ $task->id }}" class="group bg-white dark:bg-zinc-800/80 backdrop-blur-sm p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm transition-all duration-300 cursor-grab active:cursor-grabbing hover:shadow-md relative">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-zinc-900 dark:text-white leading-snug pr-6">{{ $task->title }}</h4>
                                <button wire:click="openView({{ $task->id }})" class="absolute top-4 right-4 p-1 text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <flux:icon.eye class="w-4 h-4" />
                                </button>
                            </div>
                            @if($task->description)
                                <div class="text-[10px] text-zinc-500 dark:text-zinc-400 line-clamp-2 mb-4 prose prose-zinc dark:prose-invert">
                                    {!! strip_tags($task->description) !!}
                                </div>
                            @endif
                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-zinc-100 dark:border-zinc-700/50">
                                <div class="flex items-center gap-1.5 {{ $task->deadline && $task->deadline->isPast() ? 'text-rose-500' : 'text-zinc-400' }}">
                                    <flux:icon.calendar class="w-3.5 h-3.5" />
                                    <span class="text-[10px] font-medium">{{ $task->deadline ? $task->deadline->format('d M H:i') : 'Soon' }}</span>
                                </div>
                                @if($task->attachment_path)
                                    <flux:icon.paper-clip class="w-3.5 h-3.5 text-zinc-400" />
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex-1 flex flex-col items-center justify-center text-center p-8 opacity-40">
                            <p class="text-sm font-medium">Nothing on your plate</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- In Progress Column -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between px-2">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]"></div>
                        <h3 class="font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-widest text-xs">In Progress</h3>
                        <span class="text-[10px] font-bold bg-blue-50 dark:bg-blue-900/30 text-blue-600 px-1.5 py-0.5 rounded-md">{{ $inProgressTasks->count() }}</span>
                    </div>
                </div>
                
                <div id="column-in_progress" data-status="in_progress" class="bg-blue-50/30 dark:bg-blue-900/10 border border-blue-100/50 dark:border-blue-900/30 rounded-2xl p-4 min-h-[600px] flex flex-col gap-4">
                    @forelse($inProgressTasks as $task)
                        <div wire:key="task-{{ $task->id }}" data-id="{{ $task->id }}" class="group bg-white dark:bg-zinc-800/80 backdrop-blur-sm p-4 rounded-xl border border-blue-200 dark:border-blue-800 shadow-sm transition-all duration-300 cursor-grab active:cursor-grabbing hover:shadow-indigo-100 dark:hover:shadow-indigo-900/20 relative">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-zinc-900 dark:text-white leading-snug pr-6">{{ $task->title }}</h4>
                                <button wire:click="openView({{ $task->id }})" class="absolute top-4 right-4 p-1 text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <flux:icon.eye class="w-4 h-4" />
                                </button>
                            </div>
                            @if($task->description)
                                <div class="text-[10px] text-zinc-500 dark:text-zinc-400 line-clamp-2 mb-4 prose prose-zinc dark:prose-invert">
                                    {!! strip_tags($task->description) !!}
                                </div>
                            @endif
                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-zinc-100 dark:border-zinc-700/50">
                                <div class="flex items-center gap-1.5 text-blue-500">
                                    <flux:icon.rocket-launch class="w-3.5 h-3.5" />
                                    <span class="text-[10px] font-bold uppercase">{{ $task->deadline ? $task->deadline->format('d M H:i') : 'Working' }}</span>
                                </div>
                                @if($task->attachment_path)
                                    <flux:icon.paper-clip class="w-3.5 h-3.5 text-zinc-400" />
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex-1 flex flex-col items-center justify-center text-center p-8 opacity-40">
                            <p class="text-sm font-medium">No active work items</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Done Column -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between px-2">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        <h3 class="font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-widest text-xs">Completed</h3>
                        <span class="text-[10px] font-bold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 px-1.5 py-0.5 rounded-md">{{ $doneTasks->count() }}</span>
                    </div>
                </div>
                
                <div id="column-done" data-status="done" class="bg-emerald-50/30 dark:bg-emerald-900/10 border border-emerald-100/50 dark:border-emerald-900/30 rounded-2xl p-4 min-h-[600px] flex flex-col gap-4">
                    @forelse($doneTasks as $task)
                        <div wire:key="task-{{ $task->id }}" data-id="{{ $task->id }}" class="group bg-white/60 dark:bg-zinc-800/40 backdrop-blur-sm p-4 rounded-xl border border-emerald-100 dark:border-emerald-900/20 shadow-sm opacity-80 hover:opacity-100 transition-all duration-300 relative">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-zinc-400 dark:text-zinc-500 leading-snug line-through pr-6">{{ $task->title }}</h4>
                                <button wire:click="openView({{ $task->id }})" class="absolute top-4 right-4 p-1 text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <flux:icon.eye class="w-4 h-4" />
                                </button>
                            </div>
                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-zinc-100 dark:border-zinc-700/50">
                                <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                                    <flux:icon.check-badge class="w-4 h-4" />
                                    <span class="text-[10px] font-bold uppercase tracking-tighter">Verified</span>
                                </div>
                                @if($task->rating)
                                    <div class="flex items-center bg-amber-50 dark:bg-amber-900/30 px-2 py-0.5 rounded-full border border-amber-100 dark:border-amber-800">
                                        <span class="text-[10px] font-bold text-amber-600 mr-1">{{ $task->rating }}</span>
                                        <flux:icon.star class="w-2.5 h-2.5 text-amber-500" variant="solid" />
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex-1 flex flex-col items-center justify-center text-center p-8 opacity-40">
                            <p class="text-sm font-medium">Clear for now</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    @endif
</div>

<!-- Task View Modal -->
@if($isViewModalOpen && $selectedTask)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-hidden">
    <div class="fixed inset-0 bg-gray-900/50 dark:bg-black/50 backdrop-blur-sm transition-opacity" wire:click="closeView"></div>
    <div class="relative w-full max-w-2xl mx-auto flex flex-col max-h-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between p-4 sm:p-5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
            <div class="flex items-center gap-3">
                @if($selectedTask->status === 'todo')
                    <flux:badge color="zinc" size="sm">To-Do</flux:badge>
                @elseif($selectedTask->status === 'in_progress')
                    <flux:badge color="blue" size="sm">In Progress</flux:badge>
                @else
                    <flux:badge color="green" size="sm">Done</flux:badge>
                @endif
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white leading-none">Task Details</h3>
            </div>
            <button class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300" wire:click="closeView">
                <span class="text-2xl font-semibold leading-none">&times;</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto">
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white mb-6 tracking-tight">{{ $selectedTask->title }}</h2>
            
            <div class="flex flex-wrap gap-6 mb-8">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-rose-50 dark:bg-rose-900/20 rounded-lg">
                        <flux:icon.calendar class="w-5 h-5 text-rose-500" />
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-zinc-400 tracking-wider">Deadline</p>
                        <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ $selectedTask->deadline ? $selectedTask->deadline->format('d M Y, H:i') : 'No deadline' }}</p>
                    </div>
                </div>
                
                @if($selectedTask->attachment_path)
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <flux:icon.paper-clip class="w-5 h-5 text-blue-500" />
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-zinc-400 tracking-wider">Attachment</p>
                            <a href="{{ Storage::url($selectedTask->attachment_path) }}" target="_blank" class="text-sm font-semibold text-blue-600 hover:underline dark:text-blue-400">Download File</a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="prose prose-zinc dark:prose-invert max-w-none border-t border-zinc-100 dark:border-zinc-800 pt-6">
                <p class="text-[10px] uppercase font-bold text-zinc-400 tracking-wider mb-2">Instructions</p>
                {!! $selectedTask->description ?? '<p class="text-zinc-500 italic">No instructions provided.</p>' !!}
            </div>
        </div>
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        const columns = ['todo', 'in_progress', 'done'];
        columns.forEach(status => {
            const el = document.getElementById(`column-${status}`);
            if (el) {
                new Sortable(el, {
                    group: 'tasks',
                    animation: 150,
                    ghostClass: 'opacity-50',
                    dragClass: 'rotate-2',
                    onEnd: (evt) => {
                        const taskId = evt.item.getAttribute('data-id');
                        const newStatus = evt.to.getAttribute('data-status');
                        const oldStatus = evt.from.getAttribute('data-status');
                        if (taskId && newStatus && newStatus !== oldStatus) {
                            @this.updateStatus(taskId, newStatus);
                        }
                    }
                });
            }
        });
    });
</script>
</div>
