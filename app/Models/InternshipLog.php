<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternshipLog extends Model
{
    protected $fillable = [
        'internship_id',
        'date',
        'activity',
        'photo_path',
        'is_verified',
        'mentor_notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_verified' => 'boolean',
        ];
    }

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }
}
