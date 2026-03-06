<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'purpose',
        'destination',
        'start_mileage',
        'end_mileage',
        'speedometer_photo_url',
        'loan_date',
        'return_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'loan_date' => 'datetime',
            'return_date' => 'datetime',
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

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function returnVehicle(int $endMileage, string $speedometerPhoto): void
    {
        $this->update([
            'end_mileage' => $endMileage,
            'speedometer_photo_url' => $speedometerPhoto,
            'return_date' => now(),
            'status' => 'returned',
        ]);

        // Update vehicle mileage and status
        $this->vehicle->updateMileage($endMileage);
        $this->vehicle->update(['status' => 'available']);
    }
}
