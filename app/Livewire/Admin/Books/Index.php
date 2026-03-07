<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Books;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $selectedCategory = '';

    public string $sortBy = 'recent';

    #[Layout('components.layouts.admin')]
    public function render()
    {
        $categories = Category::alphabetical()->get();

        $query = Book::with(['category', 'reviews']);

        if ($this->search) {
            $query->search($this->search);
        }

        if ($this->selectedCategory) {
            $query->byCategory((int) $this->selectedCategory);
        }

        match ($this->sortBy) {
            'popular' => $query->popular(),
            'title' => $query->orderBy('title'),
            default => $query->recent(),
        };

        $books = $query->paginate(15);

        return view('livewire.admin.books.index', [
            'books' => $books,
            'categories' => $categories,
        ])->title(__('sidebar.manage_books'));
    }

    public function deleteBook(int $bookId): void
    {
        $book = Book::findOrFail($bookId);

        // Delete associated files
        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }

        if ($book->file_path && Storage::disk('private')->exists($book->file_path)) {
            Storage::disk('private')->delete($book->file_path);
        }

        $book->delete();

        session()->flash('success', __('books.success_deleted'));
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
