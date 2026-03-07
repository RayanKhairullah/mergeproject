<?php

namespace App\Livewire\Admin\Roles;

use Illuminate\Contracts\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateRole extends Component
{
    use LivewireAlert;

    #[Validate('required|string|max:255')]
    public string $name = '';

    /** @var array<mixed> */
    #[Validate('array|min:1')]
    public array $selectedPermissions = [];

    public function mount(): void
    {
        $this->authorize('create roles');
    }

    public function createRole(): void
    {
        $this->authorize('create roles');

        $this->validate();

        $role = Role::create([
            'name' => $this->name,
        ]);

        $permissions = collect($this->selectedPermissions)->map(fn ($permission): int =>
            // convert string to int
        (int) $permission)->toArray();

        $role->syncPermissions($permissions);

        $this->flash('success', __('roles.role_created'));

        $this->redirect(route('admin.roles.index'), true);

    }

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        return view('livewire.admin.roles.create-role', [
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
