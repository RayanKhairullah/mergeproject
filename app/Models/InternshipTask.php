<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternshipTask extends Model
{
    protected $fillable = [
        'internship_id',
        'title',
        'description',
        'status',
        'deadline',
        'attachment_path',
        'rating',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
        ];
    }

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }
}
