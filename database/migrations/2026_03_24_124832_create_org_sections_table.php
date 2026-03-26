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
        Schema::create('org_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('display_mode')->default('table'); // 'tree' or 'table'
            $table->json('table_columns')->nullable(); // Store columns format
            $table->integer('order')->default(0);      // For ordering the sections
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('org_sections');
    }
};
