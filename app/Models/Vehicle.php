<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_plate',
        'image',
        'current_mileage',
        'status',
        'last_service_date',
    ];

    protected function casts(): array
    {
        return [
            'last_service_date' => 'date',
        ];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(VehicleExpense::class);
    }

    public function activeLoan(): HasMany
    {
        return $this->hasMany(Loan::class)->where('status', 'active');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function updateMileage(int $mileage): void
    {
        if ($mileage > $this->current_mileage) {
            $this->update(['current_mileage' => $mileage]);
        }
    }
}
