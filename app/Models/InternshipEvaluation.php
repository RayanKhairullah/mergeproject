<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternshipEvaluation extends Model
{
    protected $fillable = [
        'internship_id',
        'mid_term_score',
        'final_score',
        'technical_skill',
        'communication_skill',
        'teamwork_skill',
        'discipline_skill',
        'mentor_feedback',
        'intern_feedback',
        'program_feedback_intern',
        'is_completed',
        'is_passed',
        'certificate_path',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'is_passed' => 'boolean',
        ];
    }

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }
}
