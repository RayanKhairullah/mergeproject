<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternshipAttendance extends Model
{
    protected $fillable = [
        'internship_id',
        'date',
        'time_in',
        'time_out',
        'latitude_in',
        'longitude_in',
        'latitude_out',
        'longitude_out',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }
}
