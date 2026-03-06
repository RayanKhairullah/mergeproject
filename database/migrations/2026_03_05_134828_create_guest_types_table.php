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
        Schema::create('guest_types', function (Blueprint $table) {
            $table->id();
            $table->string('value')->unique();
            $table->string('label');
            $table->timestamps();
        });

        // Insert default guest types
        DB::table('guest_types')->insert([
            ['value' => 'VVIP', 'label' => 'VVIP Guest', 'created_at' => now(), 'updated_at' => now()],
            ['value' => 'VIP', 'label' => 'VIP Guest', 'created_at' => now(), 'updated_at' => now()],
            ['value' => 'Internal', 'label' => 'Internal Staff', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_types');
    }
};
