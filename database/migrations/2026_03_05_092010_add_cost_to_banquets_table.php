<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banquets', function (Blueprint $table) {
            $table->decimal('cost', 15, 2)->nullable()->after('estimated_guests');
        });
    }

    public function down(): void
    {
        Schema::table('banquets', function (Blueprint $table) {
            $table->dropColumn('cost');
        });
    }
};
