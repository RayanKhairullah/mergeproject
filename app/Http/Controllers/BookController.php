<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BookController extends Controller
{
    public function download(Book $book): BinaryFileResponse|Response
    {

        // Check if file exists
        if (! $book->file_path || ! Storage::disk('private')->exists($book->file_path)) {
            abort(404, 'Book file not found or has been removed.');
        }

        // Increment download count
        $book->incrementDownloadCount();

        // Get the full file path
        $filePath = Storage::disk('private')->path($book->file_path);

        // Get original filename for download
        $filename = pathinfo($book->file_path, PATHINFO_BASENAME);

        return response()->download($filePath, $filename);
    }

    public function read(Book $book)
    {
        if (! $book->file_path || ! Storage::disk('private')->exists($book->file_path)) {
            abort(404, 'Book file not found or has been removed.');
        }

        return view('frontend.books.read', compact('book'));
    }

    public function stream(Book $book)
    {
        if (! $book->file_path || ! Storage::disk('private')->exists($book->file_path)) {
            abort(404, 'Book file not found or has been removed.');
        }

        $filePath = Storage::disk('private')->path($book->file_path);

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($book->file_path).'"',
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');

        $books = Book::query()
            ->with(['category', 'reviews'])
            ->when($query, fn ($q) => $q->search($query))
            ->when($categoryId, fn ($q) => $q->byCategory($categoryId))
            ->recent()
            ->paginate(12);

        return response()->json([
            'books' => $books->items(),
            'pagination' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ],
        ]);
    }
}
