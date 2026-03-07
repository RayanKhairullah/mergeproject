<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showCreateForm = false;

    public ?Category $editingCategory = null;

    public string $name = '';

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        $query = Category::withBookCount()->alphabetical();

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $categories = $query->paginate(15);

        return view('livewire.admin.categories.index', [
            'categories' => $categories,
        ])->title(__('sidebar.book_categories'));
    }

    public function showCreateForm(): void
    {
        $this->showCreateForm = true;
        $this->name = '';
        $this->editingCategory = null;
    }

    public function editCategory(Category $category): void
    {
        $this->editingCategory = $category;
        $this->name = $category->name;
        $this->showCreateForm = true;
    }

    public function saveCategory(): void
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:categories,name'.($this->editingCategory ? ','.$this->editingCategory->id : ''),
        ]);

        if ($this->editingCategory) {
            $this->editingCategory->update(['name' => $this->name]);
            session()->flash('success', __('categories.success_updated'));
        } else {
            Category::create(['name' => $this->name]);
            session()->flash('success', __('categories.success_created'));
        }

        $this->showCreateForm = false;
        $this->editingCategory = null;
        $this->name = '';
    }

    public function deleteCategory(Category $category): void
    {
        // Set books' category to null if they exist
        if ($category->books()->count() > 0) {
            $category->books()->update(['category_id' => null]);
        }

        $category->delete();
        session()->flash('success', __('categories.success_deleted'));
    }

    public function cancelForm(): void
    {
        $this->showCreateForm = false;
        $this->editingCategory = null;
        $this->name = '';
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
