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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('purpose');
            $table->text('destination');
            $table->integer('start_mileage');
            $table->integer('end_mileage')->nullable();
            $table->string('speedometer_photo_url')->nullable();
            $table->timestamp('loan_date')->useCurrent();
            $table->timestamp('return_date')->nullable();
            $table->enum('status', ['active', 'returned'])->default('active');
            $table->timestamps();

            // Indexes
            $table->index('vehicle_id');
            $table->index('user_id');
            $table->index('loan_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
