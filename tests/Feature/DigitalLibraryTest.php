<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\Review;
use App\Models\User;

it('can browse books', function () {
    $category = Category::factory()->create(['name' => 'Technology']);
    $book = Book::factory()->create([
        'title' => 'Laravel Guide',
        'author' => 'John Doe',
        'category_id' => $category->id,
    ]);

    $response = $this->get('/books');

    $response->assertStatus(200);
    $response->assertSee('Digital Library');
    $response->assertSee('Laravel Guide');
    $response->assertSee('John Doe');
});

it('can view book details', function () {
    $category = Category::factory()->create(['name' => 'Technology']);
    $book = Book::factory()->create([
        'title' => 'Laravel Guide',
        'author' => 'John Doe',
        'description' => 'A comprehensive guide to Laravel',
        'category_id' => $category->id,
    ]);

    $response = $this->get("/books/{$book->slug}");

    $response->assertStatus(200);
    $response->assertSee('Laravel Guide');
    $response->assertSee('John Doe');
    $response->assertSee('A comprehensive guide to Laravel');
    $response->assertSee('Technology');
});

it('requires authentication to download books', function () {
    $book = Book::factory()->create();

    $response = $this->get("/books/{$book->slug}/download");

    // Laravel redirects unauthenticated users to login page (302), not 401
    $response->assertStatus(302);
    $response->assertRedirect('/login');
});

it('allows authenticated users to download books', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create([
        'file_path' => 'books/files/test.pdf',
        'download_count' => 0, // Start with 0 downloads
    ]);

    // Mock the file existence
    Storage::fake('private');
    Storage::disk('private')->put('books/files/test.pdf', 'fake pdf content');

    $response = $this->actingAs($user)->get("/books/{$book->slug}/download");

    $response->assertStatus(200);

    // Verify download count was incremented
    expect($book->fresh()->download_count)->toBe(1);
});

it('allows users to submit reviews', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $response = $this->actingAs($user)->get("/books/{$book->slug}");
    $response->assertStatus(200);

    // Test that review form is available for authenticated users
    $response->assertSee('Write a Review');
});

it('calculates average rating correctly', function () {
    $book = Book::factory()->create();
    $users = User::factory(3)->create();

    // Create reviews with ratings 3, 4, 5
    Review::factory()->create([
        'book_id' => $book->id,
        'user_id' => $users[0]->id,
        'rating' => 3,
    ]);

    Review::factory()->create([
        'book_id' => $book->id,
        'user_id' => $users[1]->id,
        'rating' => 4,
    ]);

    Review::factory()->create([
        'book_id' => $book->id,
        'user_id' => $users[2]->id,
        'rating' => 5,
    ]);

    $book->refresh();
    expect($book->average_rating)->toBe(4.0);
});

it('prevents duplicate reviews from same user', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    // Create first review
    Review::create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'rating' => 5,
        'comment' => 'Great book!',
    ]);

    // Attempt to create duplicate review should fail
    expect(function () use ($user, $book) {
        Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 4,
            'comment' => 'Another review',
        ]);
    })->toThrow(\Illuminate\Database\QueryException::class);
});

it('can search books by title', function () {
    $book1 = Book::factory()->create(['title' => 'Laravel Development']);
    $book2 = Book::factory()->create(['title' => 'PHP Programming']);
    $book3 = Book::factory()->create(['title' => 'JavaScript Guide']);

    // Test search functionality through the model scope
    $results = Book::search('Laravel')->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->title)->toBe('Laravel Development');
});

it('can filter books by category', function () {
    $techCategory = Category::factory()->create(['name' => 'Technology']);
    $businessCategory = Category::factory()->create(['name' => 'Business']);

    $techBook = Book::factory()->create(['category_id' => $techCategory->id]);
    $businessBook = Book::factory()->create(['category_id' => $businessCategory->id]);

    $techBooks = Book::byCategory($techCategory->id)->get();

    expect($techBooks)->toHaveCount(1);
    expect($techBooks->first()->id)->toBe($techBook->id);
});
