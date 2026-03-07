<?php

declare(strict_types=1);

namespace App\Livewire\Frontend\Books;

use App\Models\Book;
use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $selectedCategory = '';

    public string $sortBy = 'recent'; // recent, popular, title

    public string $viewMode = 'grid'; // grid, list

    #[Layout('components.layouts.app.frontend')]
    public function render()
    {
        $categories = Category::alphabetical()->get();

        $query = Book::with(['category', 'reviews']);

        // Apply search filter
        if ($this->search) {
            $query->search($this->search);
        }

        // Apply category filter
        if ($this->selectedCategory) {
            $query->byCategory((int) $this->selectedCategory);
        }

        // Apply sorting
        match ($this->sortBy) {
            'popular' => $query->popular(),
            'title' => $query->orderBy('title'),
            default => $query->recent(),
        };

        $books = $query->paginate(12);

        return view('livewire.frontend.books.index', [
            'title' => __('global.digital_library'),
            'books' => $books,
            'categories' => $categories,
        ]);
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedCategory = '';
        $this->sortBy = 'recent';
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }
}
