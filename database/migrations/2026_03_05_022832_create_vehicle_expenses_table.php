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
        Schema::create('vehicle_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reporter_name');
            $table->enum('expense_type', ['BBM', 'E-Money', 'Parkir', 'Cuci Mobil', 'Lainnya']);
            $table->enum('funding_source', ['UANG_MUKA', 'UANG_PRIBADI', 'KOPERASI_KONSUMEN_SUKA_BAHARI']);
            $table->enum('fuel_type', ['PERTALITE', 'PERTAMAX', 'PERTADEX', 'PERTAMAX TURBO', 'Lainnya'])->nullable();
            $table->decimal('fuel_liters', 10, 2)->nullable();
            $table->decimal('nominal', 12, 2);
            $table->json('documentation_photos');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('vehicle_id');
            $table->index('expense_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_expenses');
    }
};
