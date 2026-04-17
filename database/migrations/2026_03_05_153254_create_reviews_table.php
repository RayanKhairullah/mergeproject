<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('anonymous_name')->nullable();
            $table->string('anonymous_session_key')->nullable();
            $table->tinyInteger('rating')->unsigned();
            $table->text('comment')->nullable();
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();

            // Ensure one review per user per book
            $table->unique(['user_id', 'book_id']);

            // Indexes for performance
            $table->index('book_id');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
