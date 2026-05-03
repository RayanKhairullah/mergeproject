<div>

<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Intern Dashboard</h1>
            <p class="text-gray-500 mt-1 dark:text-gray-400">Welcome back, {{ auth()->user()->name }}</p>
        </div>
        @if($internship)
            <div class="flex items-center gap-2">
                @if($internship->contract_path)
                    <flux:button variant="ghost" size="sm" icon="document-text" href="{{ \Storage::url($internship->contract_path) }}" target="_blank">View Contract</flux:button>
                @endif
                @if($internship->evaluations()->where('is_passed', true)->exists())
                    <flux:button variant="primary" size="sm" icon="academic-cap" href="{{ route('intern.certificate.download', $internship->id) }}">Download Certificate</flux:button>
                @endif
                <flux:button variant="ghost" size="sm" icon="chat-bubble-left-right" wire:click="openSurvey">Program Feedback</flux:button>
            </div>
        @endif
    </div>

    @if(!$internship)
        <div class="bg-yellow-50 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 p-4 rounded-lg">
            You do not have an active internship assignment. Please contact HR.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Attendance Card -->
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm"
                 x-data="{ 
                    loading: false,
                    clockAct(type) {
                        this.loading = true;
                        if(navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(position => {
                                if (type === 'in') { @this.clockIn(position.coords.latitude, position.coords.longitude); }
                                else { @this.clockOut(position.coords.latitude, position.coords.longitude); }
                                this.loading = false;
                            }, error => {
                                alert('Geolocation failed. Please allow location access.');
                                this.loading = false;
                            });
                        } else {
                            alert('Geolocation is not supported by this browser.');
                            this.loading = false;
                        }
                    }
                 }">
                <div class="flex items-center gap-3 mb-4 text-zinc-800 dark:text-zinc-200">
                    <flux:icon.clock class="w-6 h-6 text-teal-500" />
                    <h2 class="text-lg font-medium">Today's Attendance</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center bg-zinc-50 dark:bg-zinc-800/50 p-3 rounded-lg border border-zinc-100 dark:border-zinc-800">
                        <span class="text-sm text-zinc-500 font-medium">Clock In</span>
                        <span class="font-mono text-zinc-900 dark:text-white">{{ $todayAttendance?->time_in ?? '--:--:--' }}</span>
                    </div>
                    <div class="flex justify-between items-center bg-zinc-50 dark:bg-zinc-800/50 p-3 rounded-lg border border-zinc-100 dark:border-zinc-800">
                        <span class="text-sm text-zinc-500 font-medium">Clock Out</span>
                        <span class="font-mono text-zinc-900 dark:text-white">{{ $todayAttendance?->time_out ?? '--:--:--' }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-6">
                    <flux:button variant="primary" x-on:click="clockAct('in')" :disabled="$todayAttendance !== null" class="w-full">
                        Clock In
                    </flux:button>
                    <flux:button variant="danger" x-on:click="clockAct('out')" :disabled="$todayAttendance === null || $todayAttendance?->time_out !== null" class="w-full">
                        Clock Out
                    </flux:button>
                </div>
                <div x-show="loading" class="mt-2 text-xs text-center text-teal-600 animate-pulse">Obtaining location...</div>
            </div>

            <!-- Logbook Card -->
            <div class="md:col-span-2 bg-white dark:bg-zinc-900 p-6 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="flex items-center gap-3 mb-4 text-zinc-800 dark:text-zinc-200">
                    <flux:icon.document-text class="w-6 h-6 text-blue-500" />
                    <h2 class="text-lg font-medium">Daily Logbook</h2>
                </div>

                <form wire:submit="saveLog" class="mb-6">
                    <flux:field>
                        <flux:label>What did you work on today?</flux:label>
                        <flux:textarea wire:model="activity" required placeholder="Describe your daily tasks and achievements..." class="h-24"></flux:textarea>
                        <flux:error name="activity" />
                    </flux:field>
                    <div class="mt-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div class="flex flex-col gap-2">
                            <flux:field>
                                <flux:input type="file" wire:model="photo" accept="image/*" size="sm" />
                                <flux:error name="photo" />
                            </flux:field>
                            @if ($photo)
                                <div class="relative w-20 h-20 group">
                                    <img src="{{ $photo->temporaryUrl() }}" class="w-20 h-20 object-cover rounded-lg border-2 border-teal-500 shadow-md">
                                    <button type="button" wire:click="$set('photo', null)" class="absolute -top-2 -right-2 bg-rose-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <flux:icon.x-mark class="w-3 h-3" />
                                    </button>
                                </div>
                            @endif
                        </div>
                        <flux:button type="submit" variant="primary">Submit Log</flux:button>
                    </div>
                </form>

                <h3 class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3 border-t border-zinc-200 dark:border-zinc-800 pt-4">Recent Logs</h3>
                <div class="space-y-3">
                    @forelse($logs as $log)
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-800/30 rounded-lg border border-zinc-100 dark:border-zinc-800 text-sm">
                            <div class="flex justify-between items-start mb-1">
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $log->date->format('D, M d Y') }}</span>
                                @if($log->is_verified)
                                    <flux:badge color="green" size="sm">Verified</flux:badge>
                                @else
                                    <flux:badge color="yellow" size="sm">Pending</flux:badge>
                                @endif
                            </div>
                            <div class="flex flex-col md:flex-row gap-4 items-start">
                                <div class="flex-1">
                                    <p class="text-zinc-600 dark:text-zinc-400 mt-1 whitespace-pre-wrap">{{ $log->activity }}</p>
                                </div>
                                @if($log->photo_path)
                                    <div class="shrink-0">
                                        <a href="{{ Storage::url($log->photo_path) }}" target="_blank">
                                            <img src="{{ Storage::url($log->photo_path) }}" alt="Activity Photo" class="w-16 h-16 object-cover rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm hover:scale-105 transition-transform">
                                        </a>
                                    </div>
                                @endif
                            </div>
                            @if($log->mentor_notes)
                                <div class="mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-700 text-xs text-zinc-500">
                                    <span class="font-medium">Mentor:</span> {{ $log->mentor_notes }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500 text-center py-4">No logs submitted yet.</p>
                    @endforelse
                </div>
            </div>
            
            <!-- Tasks Summary (Optional Link to Kanban) -->
            <div class="md:col-span-3 bg-white dark:bg-zinc-900 p-6 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center">
                        <flux:icon.clipboard-document-list class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-white">Task Board</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $tasksSummary['todo'] ?? 0 }} To-Do &bull; 
                            {{ $tasksSummary['in_progress'] ?? 0 }} In Progress &bull; 
                            {{ $tasksSummary['done'] ?? 0 }} Done
                        </p>
                    </div>
                </div>
                <flux:button variant="ghost" href="{{ route('intern.tasks') }}" icon-trailing="arrow-right">Open Kanban</flux:button>
            </div>
        </div>
    @endif

    <!-- Survey Modal -->
    @if($isSurveyModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-hidden">
        <div class="fixed inset-0 bg-gray-900/50 dark:bg-black/50 backdrop-blur-sm transition-opacity" wire:click="$set('isSurveyModalOpen', false)"></div>
        <div class="relative w-full max-w-lg mx-auto flex flex-col max-h-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between p-4 sm:p-5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Program Feedback Survey</h3>
                <button class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300" wire:click="$set('isSurveyModalOpen', false)">
                    <span class="text-2xl font-semibold leading-none">&times;</span>
                </button>
            </div>
            <div class="p-4 sm:p-6">
                <form wire:submit.prevent="saveSurvey" class="space-y-4">
                    <flux:field>
                        <flux:label>How was your internship experience?</flux:label>
                        <flux:textarea wire:model="surveyFeedback" placeholder="Share your thoughts on the program, mentor, and workspace..."></flux:textarea>
                        <flux:error name="surveyFeedback" />
                    </flux:field>

                    <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                        <flux:button variant="ghost" wire:click="$set('isSurveyModalOpen', false)">Cancel</flux:button>
                        <flux:button type="submit" variant="primary">Submit Feedback</flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
