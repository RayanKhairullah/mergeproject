<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'author' => fake()->name(),
            'description' => fake()->paragraphs(3, true),
            'category_id' => \App\Models\Category::factory(),
            'cover_image' => null, // Will be set when uploading actual files
            'file_path' => null, // Will be set when uploading actual files
            'download_count' => fake()->numberBetween(0, 1000),
        ];
    }
}
