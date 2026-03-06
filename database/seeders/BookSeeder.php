<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');

            return;
        }

        // Create sample books
        $sampleBooks = [
            [
                'title' => 'Introduction to Laravel Development',
                'author' => 'John Smith',
                'description' => 'A comprehensive guide to building modern web applications with Laravel framework. This book covers everything from basic concepts to advanced techniques.',
                'category' => 'Technology',
            ],
            [
                'title' => 'Advanced PHP Programming',
                'author' => 'Jane Doe',
                'description' => 'Master advanced PHP concepts and design patterns. Learn how to write clean, maintainable, and scalable PHP code.',
                'category' => 'Technology',
            ],
            [
                'title' => 'Database Design Fundamentals',
                'author' => 'Mike Johnson',
                'description' => 'Learn the principles of database design, normalization, and optimization. Essential reading for any developer.',
                'category' => 'Technology',
            ],
            [
                'title' => 'The Art of Business Strategy',
                'author' => 'Sarah Wilson',
                'description' => 'Explore strategic thinking and business planning. A must-read for entrepreneurs and business leaders.',
                'category' => 'Business',
            ],
            [
                'title' => 'Modern Physics Explained',
                'author' => 'Dr. Robert Brown',
                'description' => 'An accessible introduction to quantum mechanics, relativity, and modern physics concepts.',
                'category' => 'Physics',
            ],
        ];

        foreach ($sampleBooks as $bookData) {
            $category = $categories->where('name', $bookData['category'])->first();

            Book::firstOrCreate(
                ['title' => $bookData['title']],
                [
                    'author' => $bookData['author'],
                    'description' => $bookData['description'],
                    'category_id' => $category?->id,
                    'download_count' => fake()->numberBetween(0, 500),
                ]
            );
        }

        // Create additional random books
        Book::factory(20)->create([
            'category_id' => fn () => $categories->random()->id,
        ]);
    }
}
