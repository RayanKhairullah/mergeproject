<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        if ($users->isEmpty() || $books->isEmpty()) {
            $this->command->warn('No users or books found. Please ensure users and books exist before seeding reviews.');

            return;
        }

        // Create reviews for each book (not all users will review all books)
        foreach ($books as $book) {
            $reviewCount = fake()->numberBetween(0, min(5, $users->count()));
            $selectedUsers = $users->random($reviewCount);

            foreach ($selectedUsers as $user) {
                Review::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'book_id' => $book->id,
                    ],
                    [
                        'rating' => fake()->numberBetween(1, 5),
                        'comment' => fake()->optional(0.7)->paragraph(),
                    ]
                );
            }
        }
    }
}
