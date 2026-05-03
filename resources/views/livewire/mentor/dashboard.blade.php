<div>

<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Mentor Dashboard</h1>
        <p class="text-gray-500 mt-1 dark:text-gray-400">Manage and evaluate your assigned interns.</p>
    </div>

    @if($internships->isEmpty())
        <div class="bg-zinc-50 dark:bg-zinc-900/50 p-8 rounded-xl border border-zinc-200 dark:border-zinc-800 text-center text-zinc-500">
            You do not have any active interns assigned to you.
        </div>
    @else
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Sidebar / List of Interns -->
            <div class="w-full md:w-1/4">
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-800/80 font-medium text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-800">
                        My Interns
                    </div>
                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($internships as $internship)
                            <button wire:click="selectInternship({{ $internship->id }})" 
                                    class="w-full text-left p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors {{ $activeInternshipId === $internship->id ? 'bg-teal-50 dark:bg-teal-900/20 border-l-4 border-teal-500' : '' }}">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $internship->user->name }}</div>
                                <div class="text-xs text-zinc-500 mt-1">{{ $internship->position }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Workspace -->
            <div class="w-full md:w-3/4 space-y-6">
                @if($activeIntern)
                    <!-- Toolbar -->
                    <div class="flex justify-between items-center bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-teal-100 dark:bg-teal-900/50 flex items-center justify-center text-teal-600 dark:text-teal-400 font-bold">
                                {{ substr($activeIntern->user->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-medium text-zinc-900 dark:text-gray-100">{{ $activeIntern->user->name }}</h3>
                                <div class="text-xs text-zinc-500">{{ $activeIntern->department }} &bull; {{ $activeIntern->start_date->format('M d') }} to {{ $activeIntern->end_date->format('M d') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button variant="ghost" size="sm" icon="academic-cap" wire:click="openEvaluation">Evaluation</flux:button>
                            <flux:button variant="primary" size="sm" icon="plus" wire:click="createTask">Assign Task</flux:button>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Tasks Box -->
                        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-5">
                            <h3 class="font-medium text-zinc-800 dark:text-zinc-200 mb-4 flex items-center gap-2">
                                <flux:icon.clipboard-document-check class="w-5 h-5 text-indigo-500" /> Tasks
                            </h3>
                            <div class="space-y-3">
                                @forelse($activeIntern->tasks as $task)
                                    <div class="p-3 bg-zinc-50 dark:bg-zinc-800/30 rounded-lg border border-zinc-100 dark:border-zinc-800 text-sm">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="font-medium text-zinc-900 dark:text-zinc-100 {{ $task->status === 'done' ? 'line-through opacity-70' : '' }}">{{ $task->title }}</span>
                                            @if($task->status === 'todo')
                                                <flux:badge color="zinc" size="sm">To-Do</flux:badge>
                                            @elseif($task->status === 'in_progress')
                                                <flux:badge color="blue" size="sm">In Progress</flux:badge>
                                            @else
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-4 mt-2 text-[10px] text-zinc-500">
                                            @if($task->deadline)
                                                <div class="flex items-center gap-1">
                                                    <flux:icon.calendar class="w-3 h-3" />
                                                    {{ $task->deadline->format('d M H:i') }}
                                                </div>
                                            @endif
                                            @if($task->attachment_path)
                                                <a href="{{ Storage::url($task->attachment_path) }}" target="_blank" class="flex items-center gap-1 text-blue-600 hover:underline">
                                                    <flux:icon.paper-clip class="w-3 h-3" />
                                                    Attachment
                                                </a>
                                            @endif
                                        </div>
                                        @if($task->status === 'done' && !$task->rating)
                                            <div class="mt-3 pt-3 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                                                <span class="text-xs text-zinc-500">Rate this task:</span>
                                                <div class="flex gap-1">
                                                    @for($i=1; $i<=5; $i++)
                                                        <button wire:click="rateTask({{ $task->id }}, {{ $i }})" class="p-1 hover:text-yellow-500 text-zinc-300 dark:text-zinc-600 transition-colors">
                                                            <flux:icon.star class="w-4 h-4" />
                                                        </button>
                                                    @endfor
                                                </div>
                                            </div>
                                        @elseif($task->status === 'done' && $task->rating)
                                            <div class="mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-700 flex items-center gap-1 text-xs text-yellow-500 font-medium">
                                                {{ $task->rating }} <flux:icon.star class="w-3 h-3" fill="currentColor" /> Rated
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-sm text-zinc-400">No tasks assigned.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Logs Box -->
                        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-5">
                            <h3 class="font-medium text-zinc-800 dark:text-zinc-200 mb-4 flex items-center gap-2">
                                <flux:icon.document-text class="w-5 h-5 text-blue-500" /> Daily Logs
                            </h3>
                            <div class="space-y-3">
                                @forelse($activeIntern->logs as $log)
                                    <div class="p-3 bg-zinc-50 dark:bg-zinc-800/30 rounded-lg border border-zinc-100 dark:border-zinc-800 text-sm">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $log->date->format('D, M d') }}</span>
                                            @if($log->is_verified)
                                                <flux:badge color="green" size="sm">Verified</flux:badge>
                                            @else
                                                <flux:button size="sm" variant="primary" wire:click="verifyLog({{ $log->id }})">Verify</flux:button>
                                            @endif
                                        </div>
                                        <div class="flex flex-col md:flex-row gap-4 items-start">
                                            <div class="flex-1">
                                                <p class="text-zinc-600 dark:text-zinc-400 whitespace-pre-wrap">{{ $log->activity }}</p>
                                            </div>
                                            @if($log->photo_path)
                                                <div class="shrink-0">
                                                    <a href="{{ Storage::url($log->photo_path) }}" target="_blank">
                                                        <img src="{{ Storage::url($log->photo_path) }}" alt="Activity Photo" class="w-16 h-16 object-cover rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm hover:scale-105 transition-transform">
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-zinc-400">No logs submitted yet.</p>
                                @endforelse
                            </div>
                        </div>

                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Assign Task Modal -->
    @if($isTaskModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-hidden">
        <div class="fixed inset-0 bg-gray-900/50 dark:bg-black/50 backdrop-blur-sm transition-opacity" wire:click="$set('isTaskModalOpen', false)"></div>
        <div class="relative w-full max-w-lg mx-auto flex flex-col max-h-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between p-4 sm:p-5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Assign Task</h3>
                <button class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300" wire:click="$set('isTaskModalOpen', false)">
                    <span class="text-2xl font-semibold leading-none">&times;</span>
                </button>
            </div>
            <div class="p-4 sm:p-6">
                <form wire:submit.prevent="saveTask" class="space-y-4">
                    <flux:field>
                        <flux:label>Title</flux:label>
                        <flux:input wire:model="taskTitle" required placeholder="Task title..." />
                        <flux:error name="taskTitle" />
                    </flux:field>
                    
                    <flux:field>
                        <flux:label>Description (Optional)</flux:label>
                        <x-rich-text wire:model="taskDescription" placeholder="Task details..." />
                        <flux:error name="taskDescription" />
                    </flux:field>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Deadline (Optional)</flux:label>
                            <flux:input type="datetime-local" wire:model="taskDeadline" />
                            <flux:error name="taskDeadline" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Attachment (Optional)</flux:label>
                            <flux:input type="file" wire:model="taskFile" />
                            <flux:error name="taskFile" />
                        </flux:field>
                    </div>

                    <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                        <flux:button variant="ghost" wire:click="$set('isTaskModalOpen', false)">Cancel</flux:button>
                        <flux:button type="submit" variant="primary">Assign</flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Evaluation Modal -->
    @if($isEvalModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-hidden">
        <div class="fixed inset-0 bg-gray-900/50 dark:bg-black/50 backdrop-blur-sm transition-opacity" wire:click="$set('isEvalModalOpen', false)"></div>
        <div class="relative w-full max-w-2xl mx-auto flex flex-col max-h-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between p-4 sm:p-5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Final Evaluation Rubric</h3>
                <button class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300" wire:click="$set('isEvalModalOpen', false)">
                    <span class="text-2xl font-semibold leading-none">&times;</span>
                </button>
            </div>
            <div class="p-4 sm:p-6 overflow-y-auto">
                <form wire:submit.prevent="saveEvaluation" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <flux:field>
                            <flux:label>Technical Skills (1-5)</flux:label>
                            <flux:select wire:model="evalTechnical">
                                @for($i=1;$i<=5;$i++) <option value="{{$i}}">{{$i}}</option> @endfor
                            </flux:select>
                        </flux:field>
                        <flux:field>
                            <flux:label>Communication (1-5)</flux:label>
                            <flux:select wire:model="evalCommunication">
                                @for($i=1;$i<=5;$i++) <option value="{{$i}}">{{$i}}</option> @endfor
                            </flux:select>
                        </flux:field>
                        <flux:field>
                            <flux:label>Teamwork (1-5)</flux:label>
                            <flux:select wire:model="evalTeamwork">
                                @for($i=1;$i<=5;$i++) <option value="{{$i}}">{{$i}}</option> @endfor
                            </flux:select>
                        </flux:field>
                        <flux:field>
                            <flux:label>Discipline (1-5)</flux:label>
                            <flux:select wire:model="evalDiscipline">
                                @for($i=1;$i<=5;$i++) <option value="{{$i}}">{{$i}}</option> @endfor
                            </flux:select>
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>Overall Feedback</flux:label>
                        <flux:textarea wire:model="evalFeedback" placeholder="What was the intern's contribution..."></flux:textarea>
                        <flux:error name="evalFeedback" />
                    </flux:field>

                    <flux:checkbox wire:model="evalPassed" label="Met program requirements for certificate issuance" />

                    <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                        <flux:button variant="ghost" wire:click="$set('isEvalModalOpen', false)">Cancel</flux:button>
                        <flux:button type="submit" variant="primary">Save Evaluation</flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
