<section class="w-full">
    <x-page-heading>
        <x-slot:title>
            {{ __('roles.edit_role') }}
        </x-slot:title>
        <x-slot:subtitle>
            {{ __('roles.edit_role_description') }}
        </x-slot:subtitle>

    </x-page-heading>

    <x-form wire:submit="editRole" class="space-y-6">
        <flux:input wire:model.live="name" label="{{ __('roles.name') }}"/>

<flux:checkbox.group wire:model.live="selectedPermissions" label="{{ __('roles.permissions') }}" description="{{ __('roles.permissions_description') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($groupedPermissions as $category => $permissions)
            <div class="col-span-full">
                <h4 class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $category }}</h4>
            </div>

            @foreach($permissions as $permission)
                <flux:checkbox label="{{ $permission->name }}" value="{{ $permission->id }}"/>
            @endforeach
            @endforeach
        </flux:checkbox.group>

        <flux:button type="submit" icon="save" variant="primary">
            {{ __('roles.update_role') }}
        </flux:button>
    </x-form>
</section>
