<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index()
    {
        return view('livewire.admin.books.index');
    }

    public function create()
    {
        $categories = Category::alphabetical()->get();

        return view('admin.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:100',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'book_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
        ]);

        $book = new Book($validated);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('books/covers', 'public');
            $book->cover_image = $coverPath;
        }

        // Handle book file upload
        if ($request->hasFile('book_file')) {
            $filePath = $request->file('book_file')->store('books/files', 'private');
            $book->file_path = $filePath;
        }

        $book->save();

        return redirect()->route('admin.books.index')
            ->with('success', 'Book created successfully!');
    }

    public function edit(Book $book)
    {
        $categories = Category::alphabetical()->get();

        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:100',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'book_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $book->fill($validated);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old cover image
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }

            $coverPath = $request->file('cover_image')->store('books/covers', 'public');
            $book->cover_image = $coverPath;
        }

        // Handle book file upload
        if ($request->hasFile('book_file')) {
            // Delete old book file
            if ($book->file_path && Storage::disk('private')->exists($book->file_path)) {
                Storage::disk('private')->delete($book->file_path);
            }

            $filePath = $request->file('book_file')->store('books/files', 'private');
            $book->file_path = $filePath;
        }

        $book->save();

        return redirect()->route('admin.books.index')
            ->with('success', 'Book updated successfully!');
    }

    public function destroy(Book $book)
    {
        // Delete associated files
        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('private')->delete($book->cover_image);
        }

        if ($book->file_path && Storage::disk('private')->exists($book->file_path)) {
            Storage::disk('private')->delete($book->file_path);
        }

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Book deleted successfully!');
    }
}
