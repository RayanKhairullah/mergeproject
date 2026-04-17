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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate', 15)->unique();
            $table->string('image')->nullable();
            $table->integer('current_mileage')->default(0);
            $table->enum('status', ['available', 'in_use', 'maintenance'])->default('available');
            $table->date('last_service_date')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('license_plate');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
