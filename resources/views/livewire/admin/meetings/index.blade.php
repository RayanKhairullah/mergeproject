<div class="space-y-6 px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-zinc-950 dark:text-white">
                {{ __('meetings.manage_meetings') }}
            </h1>
            <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 mt-1">
                {{ __('meetings.schedule_management') }}
            </p>
        </div>
        @can('create meetings')
            <flux:modal.trigger name="create-meeting">
                <flux:button variant="primary" icon="plus" class="w-full md:w-auto">
                    {{ __('meetings.create') }}
                </flux:button>
            </flux:modal.trigger>
        @endcan
    </div>

    @can('create meetings')
        <livewire:admin.meetings.create-meeting-modal />
    @endcan

    @foreach($meetings as $meeting)
        @can('update meetings')
            <livewire:admin.meetings.edit-meeting-modal :meetingId="$meeting->id" wire:key="edit-meeting-{{ $meeting->id }}" />
        @endcan
    @endforeach

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-8">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('meetings.search_placeholder') }}" icon="magnifying-glass" />
        
        <flux:select wire:model.live="statusFilter" placeholder="{{ __('meetings.all_status') }}">
            <flux:select.option value="">{{ __('meetings.all_status') }}</flux:select.option>
            <flux:select.option value="DRAFT">{{ __('meetings.status.draft') }}</flux:select.option>
            <flux:select.option value="PENDING_APPROVAL">{{ __('meetings.status.pending_approval') }}</flux:select.option>
            <flux:select.option value="PUBLISHED">{{ __('meetings.status.published') }}</flux:select.option>
            <flux:select.option value="COMPLETED">{{ __('meetings.status.completed') }}</flux:select.option>
            <flux:select.option value="REJECTED">{{ __('meetings.status.rejected') }}</flux:select.option>
        </flux:select>
 
        <flux:select wire:model.live="roomFilter" placeholder="{{ __('meetings.all_rooms') }}">
            <flux:select.option value="">{{ __('meetings.all_rooms') }}</flux:select.option>
            @foreach($rooms as $room)
                <flux:select.option value="{{ $room->id }}">{{ $room->name }}</flux:select.option>
            @endforeach
        </flux:select>
 
        <flux:input type="date" wire:model.live="dateFilter" />
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <flux:button wire:click="exportExcel" variant="outline" icon="arrow-down-tray" size="sm">
            <span class="hidden sm:inline">{{ __('global.export_excel') ?? 'Export Excel' }}</span>
            <span class="sm:hidden">Excel</span>
        </flux:button>
        <flux:button wire:click="exportPdf" variant="outline" icon="document-text" size="sm">
            <span class="hidden sm:inline">{{ __('global.export_pdf') ?? 'Export PDF' }}</span>
            <span class="sm:hidden">PDF</span>
        </flux:button>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('global.id') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('meetings.fields.title') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('meetings.fields.room') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('meetings.fields.started_at') }}</th>
                        <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('meetings.fields.duration') }}</th>
                        <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('meetings.fields.estimated_participants') }}</th>
                        <th class="hidden xl:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('global.notes') ?? 'Catatan' }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('meetings.fields.status') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($meetings as $meeting)
                        <tr wire:key="meeting-{{ $meeting->id }}">
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-mono">
                                #{{ $meeting->id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $meeting->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $meeting->room->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $meeting->started_at?->translatedFormat('l, d M Y - H:i') }}
                            </td>
                            <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $meeting->duration }} {{ __('meetings.duration_unit') }}
                            </td>
                            <td class="hidden lg:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $meeting->estimated_participants }} {{ __('meetings.person') }}
                            </td>
                            <td class="hidden xl:table-cell px-6 py-4 text-sm text-gray-900 dark:text-gray-100 max-w-[200px] truncate" title="{{ strip_tags(str_replace(['<br>', '</p>', '</li>'], ' ', $meeting->notes)) }}">
                                {{ empty(strip_tags($meeting->notes)) ? '-' : str(strip_tags(str_replace(['<br>', '</p>', '</li>'], ' ', $meeting->notes)))->limit(40) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge variant="{{ $meeting->status->color() }}">
                                    {{ $meeting->status->label() }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" icon="eye" wire:click="showDetail({{ $meeting->id }})">
                                        <span class="hidden md:inline">{{ __('meetings.detail') }}</span>
                                    </flux:button>
                                    @if($meeting->status->value === 'DRAFT' && (auth()->user()->can('approve meetings') || $meeting->created_by === auth()->id()))
                                        <flux:button size="sm" variant="ghost" icon="paper-airplane" wire:click="publish({{ $meeting->id }})">
                                            <span class="hidden md:inline">{{ __('meetings.publish') }}</span>
                                        </flux:button>
                                    @endif
                                    @can('update meetings')
                                        @if((in_array($meeting->status->value, ['DRAFT', 'PENDING_APPROVAL']) && (auth()->user()->can('approve meetings') || $meeting->created_by === auth()->id())) || (auth()->user()->can('approve meetings') && $meeting->status->value === 'PUBLISHED'))
                                            <flux:modal.trigger name="edit-meeting-{{ $meeting->id }}">
                                                <flux:button size="sm" variant="ghost" icon="pencil-square">
                                                    <span class="hidden md:inline">{{ __('global.view') }}</span>
                                                </flux:button>
                                            </flux:modal.trigger>
                                        @endif
                                    @endcan
                                    @if($meeting->status->value === 'PENDING_APPROVAL' && auth()->user()->can('approve meetings'))
                                        <flux:button size="sm" variant="primary" icon="check" wire:click="approve({{ $meeting->id }})">
                                            <span class="hidden md:inline">{{ __('meetings.approve') }}</span>
                                        </flux:button>
                                    @endif
                                    @can('delete meetings')
                                        @if((in_array($meeting->status->value, ['DRAFT', 'PENDING_APPROVAL']) && $meeting->created_by === auth()->id()) || auth()->user()->can('approve meetings'))
                                            <flux:button size="sm" variant="danger" icon="trash" wire:click="delete({{ $meeting->id }})" wire:confirm="{{ __('meetings.delete_confirm') }}">
                                                <span class="hidden md:inline">{{ __('global.delete') }}</span>
                                            </flux:button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('meetings.no_meetings_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $meetings->links() }}
        </div>
    </div>

    @if($detailMeeting)
        <flux:modal wire:model="detailId" class="w-full max-w-2xl">
            <div class="space-y-4">
                <flux:heading size="lg">{{ __('meetings.summary') }}</flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('meetings.fields.title') }}</p>
                        <p class="font-medium">{{ $detailMeeting->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('meetings.fields.room') }}</p>
                        <p class="font-medium">{{ $detailMeeting->room->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('meetings.room_capacity') }}</p>
                        <p class="font-medium">{{ $detailMeeting->room->capacity }} {{ __('meetings.person') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('meetings.fields.estimated_participants') }}</p>
                        <p class="font-medium">{{ $detailMeeting->estimated_participants }} {{ __('meetings.person') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('meetings.fields.started_at') }}</p>
                        <p class="font-medium">{{ $detailMeeting->started_at?->translatedFormat('l, d M Y - H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('meetings.fields.ended_at') }}</p>
                        <p class="font-medium">{{ $detailMeeting->ended_at?->translatedFormat('l, d M Y - H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('meetings.fields.duration') }}</p>
                        <p class="font-medium">{{ $detailMeeting->duration }} {{ __('meetings.duration_unit') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('meetings.fields.status') }}</p>
                        <flux:badge variant="{{ $detailMeeting->status->color() }}">
                            {{ $detailMeeting->status->label() }}
                        </flux:badge>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('meetings.fields.created_by') }}</p>
                        <p class="font-medium">{{ $detailMeeting->creator->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('meetings.fields.show_notes_on_monitor') }}</p>
                        <p class="font-medium">{{ $detailMeeting->show_notes_on_monitor ? __('meetings.yes') : __('meetings.no') }}</p>
                    </div>
                </div>

                @if($detailMeeting->notes)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('meetings.fields.notes') }}</p>
                        <div class="text-sm prose prose-sm max-w-none">
                            {!! $detailMeeting->notes !!}
                        </div>
                    </div>
                @endif

                @if($detailMeeting->approver)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('meetings.fields.approved_by') }}</p>
                            <p class="font-medium">{{ $detailMeeting->approver->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('meetings.fields.approved_at') }}</p>
                            <p class="font-medium">{{ $detailMeeting->approved_at?->translatedFormat('l, d M Y - H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if($detailMeeting->rejection_reason)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('meetings.fields.rejection_reason') }}</p>
                        <p class="text-sm text-red-600">{{ $detailMeeting->rejection_reason }}</p>
                    </div>
                @endif

                <div class="flex justify-end">
                    <flux:button wire:click="closeDetail">{{ __('meetings.close') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
