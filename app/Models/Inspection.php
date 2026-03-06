<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'admin_id',
        'inspection_time',
        'tire_condition',
        'body_condition',
        'glass_condition',
        'issue_photos',
        'mileage_check',
        'speedometer_photo_url',
        'additional_notes',
    ];

    protected function casts(): array
    {
        return [
            'issue_photos' => 'array',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'admin_id');
    }

    protected static function booted(): void
    {
        static::created(function (Inspection $inspection) {
            // Auto-update vehicle mileage after inspection
            $inspection->vehicle->updateMileage($inspection->mileage_check);
        });
    }
}
