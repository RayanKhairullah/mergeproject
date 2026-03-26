@props(['employee'])

<div class="flex flex-col items-center">
    <!-- Card Content -->
    <div class="group relative p-4 flex flex-col items-center gap-2 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm bg-white dark:bg-zinc-800 hover:shadow-md transition-shadow min-w-[200px] z-10">
        <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-white shadow-sm ring-2 ring-zinc-100 dark:ring-zinc-700">
            <img src="{{ $employee->image_url }}" alt="{{ $employee->name }}" class="object-cover w-full h-full">
        </div>
        <div class="text-center mt-1">
            <p class="font-bold text-zinc-900 dark:text-zinc-100 whitespace-nowrap">{{ $employee->name }}</p>
            <p class="text-xs font-semibold text-accent mt-0.5">{{ $employee->position ?? 'No Position' }}</p>
            @if($employee->division)
                <p class="text-[10px] text-zinc-500 bg-zinc-100 dark:bg-zinc-700 dark:text-zinc-300 py-0.5 px-2 rounded-full mt-1.5 inline-block">{{ $employee->division->name }}</p>
            @endif
        </div>
        
        <!-- CMS Actions hover (Only in Admin) -->
        @if(str_contains(request()->route()->getName(), 'admin.'))
        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col gap-1">
            <button wire:click.stop="edit({{ $employee->id }})" class="p-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-700 dark:hover:bg-zinc-600 rounded-md text-zinc-600 dark:text-zinc-300 transition-colors shadow-sm" title="Edit">
                <flux:icon.pencil class="w-3.5 h-3.5" />
            </button>
        </div>
        @endif
    </div>

    <!-- Connector lines rendering logic -->
    @if($employee->children->count() > 0)
        <!-- Vertical line going down from current node -->
        <div class="w-px h-8 bg-zinc-300 dark:bg-zinc-600"></div>

        <div class="relative flex justify-center pb-2">
            <!-- Horizontal line connecting children (only if > 1 child) -->
            @if($employee->children->count() > 1)
                <!-- the container uses border-t to draw the horizontal line half way down -->
                <div class="absolute top-0 w-[calc(100%-calc(100%/{{ $employee->children->count() }}))] border-t-2 border-zinc-300 dark:border-zinc-600 left-1/2 -translate-x-1/2 -z-10"></div>
            @endif

            <div class="flex flex-row justify-center gap-8">
                @foreach($employee->children->where('show_in_tree', true) as $child)
                    <div class="relative flex flex-col items-center">
                        <!-- Short vertical tick down to the child card -->
                        @if($employee->children->count() > 1)
                            <div class="w-px h-4 bg-zinc-300 dark:bg-zinc-600"></div>
                        @endif
                        @include('livewire.admin.employees.tree-node', ['employee' => $child])
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
