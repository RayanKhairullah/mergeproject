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
        Schema::table('org_sections', function (Blueprint $table) {
            if (! Schema::hasColumn('org_sections', 'name')) {
                $table->string('name')->after('id');
                $table->string('display_mode')->default('table')->after('name');
                $table->json('table_columns')->nullable()->after('display_mode');
                $table->integer('order')->default(0)->after('table_columns');
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'org_section_id')) {
                $table->foreignId('org_section_id')->nullable()->constrained('org_sections')->nullOnDelete();
                $table->json('custom_fields')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'org_section_id')) {
                $table->dropForeign(['org_section_id']);
                $table->dropColumn(['org_section_id', 'custom_fields']);
            }
        });

        Schema::table('org_sections', function (Blueprint $table) {
            if (Schema::hasColumn('org_sections', 'name')) {
                $table->dropColumn(['name', 'display_mode', 'table_columns', 'order']);
            }
        });
    }
};
