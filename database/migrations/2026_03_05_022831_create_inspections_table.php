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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('inspection_time', ['morning', 'afternoon']);
            $table->text('tire_condition')->default('Baik');
            $table->text('body_condition')->default('Baik');
            $table->text('glass_condition')->default('Baik');
            $table->json('issue_photos')->nullable();
            $table->integer('mileage_check');
            $table->string('speedometer_photo_url');
            $table->text('additional_notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('vehicle_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
