<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banquets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('guest_type', 100);
            $table->foreignId('venue_id')->constrained('dining_venues')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('estimated_guests')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->enum('status', ['DRAFT', 'PENDING_APPROVAL', 'PUBLISHED', 'COMPLETED', 'REJECTED'])->default('DRAFT');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['status', 'scheduled_at']);
            $table->index('venue_id');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banquets');
    }
};
