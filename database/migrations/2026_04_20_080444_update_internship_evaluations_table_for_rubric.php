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
        Schema::table('internship_evaluations', function (Blueprint $table) {
            $table->unsignedTinyInteger('technical_skill')->nullable()->after('final_score');
            $table->unsignedTinyInteger('communication_skill')->nullable()->after('technical_skill');
            $table->unsignedTinyInteger('teamwork_skill')->nullable()->after('communication_skill');
            $table->unsignedTinyInteger('discipline_skill')->nullable()->after('teamwork_skill');
            $table->string('program_feedback_intern')->nullable()->after('intern_feedback');
            $table->boolean('is_passed')->default(false)->after('is_completed');
            $table->string('certificate_path')->nullable()->after('is_passed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internship_evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'technical_skill', 'communication_skill', 'teamwork_skill', 
                'discipline_skill', 'program_feedback_intern', 'is_passed', 'certificate_path'
            ]);
        });
    }
};
