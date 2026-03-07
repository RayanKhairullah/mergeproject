<?php

declare(strict_types=1);

namespace App\Livewire\Frontend\Books;

use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use Livewire\Component;

class Show extends Component
{
    public Book $book;

    public ?\App\Models\Review $userReview = null;

    public bool $showReviewForm = false;

    public int $rating = 5;

    public string $comment = '';

    public string $anonymous_name = 'Anonymous';

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    public function mount(Book $book): void
    {
        $this->book = $book->load(['category', 'reviews.user']);

        if (Auth::check()) {
            $this->userReview = \App\Models\Review::where('user_id', Auth::id())
                ->where('book_id', $book->id)
                ->first();
        } else {
            $this->userReview = \App\Models\Review::where('anonymous_session_key', session()->getId())
                ->where('book_id', $book->id)
                ->first();
            
            $this->anonymous_name = $this->userReview?->anonymous_name ?? 'Anonymous';
        }

        if ($this->userReview) {
            $this->rating = $this->userReview->rating;
            $this->comment = $this->userReview->comment ?? '';
        }
    }

    public function render()
    {
        $this->book->loadMissing(['category', 'reviews.user']);
        
        return view('livewire.frontend.books.show');
    }

    public function submitReview(): void
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'anonymous_name' => Auth::check() ? 'nullable|string|max:255' : 'required|string|max:255',
        ], [
            'anonymous_name.required' => 'Nama (Anonim) wajib diisi untuk ulasan tanpa login.',
        ]);

        if ($this->userReview) {
            // Check time limit
            if (!$this->userReview->canBeEdited()) {
                session()->flash('error', 'Batas waktu 10 menit untuk mengedit ulasan telah berakhir.');
                return;
            }

            // Update existing review
            $this->userReview->update([
                'rating' => $this->rating,
                'comment' => $this->comment,
                'anonymous_name' => Auth::check() ? null : $this->anonymous_name,
                'user_id' => Auth::id() ?? $this->userReview->user_id, // If they log in later, we don't necessarily update but we could.
                'edited_at' => now(),
            ]);

            session()->flash('success', 'Review updated successfully!');
        } else {
            // Create new review
            $this->userReview = \App\Models\Review::create([
                'user_id' => Auth::id() ?? null,
                'book_id' => $this->book->id,
                'rating' => $this->rating,
                'comment' => $this->comment,
                'anonymous_name' => Auth::check() ? null : $this->anonymous_name,
                'anonymous_session_key' => Auth::check() ? null : session()->getId(),
            ]);

            session()->flash('success', 'Review submitted successfully!');
        }

        $this->showReviewForm = false;
        $this->book->refresh();
        $this->book->load(['reviews.user']);
    }

    public function deleteReview(): void
    {
        if ($this->userReview) {
            if (!$this->userReview->canBeEdited()) {
                session()->flash('error', 'Batas waktu 10 menit untuk menghapus ulasan telah berakhir.');
                return;
            }

            $this->userReview->delete();
            $this->userReview = null;
            $this->rating = 5;
            $this->comment = '';
            $this->anonymous_name = 'Anonymus';

            $this->book->refresh();
            $this->book->load(['reviews.user']);

            session()->flash('success', 'Review deleted successfully!');
        }
    }

    public function toggleReviewForm(): void
    {
        $this->showReviewForm = ! $this->showReviewForm;
    }
}
