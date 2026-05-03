<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    protected $fillable = [
        'user_id',
        'mentor_id',
        'division_id',
        'position',
        'department',
        'start_date',
        'end_date',
        'contract_path',
        'status',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function tasks()
    {
        return $this->hasMany(InternshipTask::class);
    }

    public function logs()
    {
        return $this->hasMany(InternshipLog::class);
    }

    public function attendances()
    {
        return $this->hasMany(InternshipAttendance::class);
    }

    public function evaluations()
    {
        return $this->hasMany(InternshipEvaluation::class);
    }
}
