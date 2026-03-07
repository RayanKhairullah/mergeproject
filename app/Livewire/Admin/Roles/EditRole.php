<?php

namespace App\Livewire\Admin\Roles;

use Illuminate\Contracts\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EditRole extends Component
{
    use LivewireAlert;

    public Role $role;

    #[Validate('required|string|max:255')]
    public string $name = '';

    /** @var array<mixed> */
    #[Validate('array|min:1')]
    public array $selectedPermissions = [];

    public function mount(Role $role): void
    {
        $this->authorize('update roles');

        $this->role = $role;

        $this->name = $role->name;

        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();

    }

    public function editRole(): void
    {
        $this->authorize('update roles');

        $this->validate();

        $this->role->update([
            'name' => $this->name,
        ]);

        // convert string to int
        $permissions = collect($this->selectedPermissions)->map(fn ($permission): int => (int) $permission)->toArray();

        $this->role->syncPermissions($permissions);

        $this->flash('success', __('roles.role_updated'));

        $this->redirect(route('admin.roles.index'), true);
    }

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        return view('livewire.admin.roles.edit-role', [
            'groupedPermissions' => $this->groupPermissions(Permission::all()),
        ]);
    }

    /**
     * Group permissions by a logical category name.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<Permission>  $permissions
     * @return array<string, \Illuminate\Database\Eloquent\Collection<Permission>>
     */
    private function groupPermissions($permissions): array
    {
        $groups = $permissions->groupBy(fn (Permission $permission): string => $this->permissionCategory($permission->name));

        $order = [
            'Dashboard',
            'Users',
            'Roles',
            'Permissions',
            'Vehicles',
            'Loans',
            'Inspections',
            'Expenses',
            'Meetings',
            'Banquets',
            'Rooms',
            'Dining Venues',
            'Books',
            'Categories',
            'Other',
        ];

        $sorted = [];

        foreach ($order as $key) {
            if ($groups->has($key)) {
                $sorted[$key] = $groups->get($key);
                $groups->forget($key);
            }
        }

        // Put any remaining groups at the end in alphabetical order
        foreach ($groups->sortKeys() as $key => $value) {
            $sorted[$key] = $value;
        }

        return $sorted;
    }

    private function permissionCategory(string $permissionName): string
    {
        $parts = explode(' ', $permissionName);

        if (count($parts) === 1) {
            return 'Other';
        }

        // Use the last word (usually the resource) as the category.
        $category = ucfirst(str_replace('_', ' ', $parts[count($parts) - 1]));

        return $category;
    }
}
