<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'reporter_name',
        'expense_type',
        'funding_source',
        'fuel_type',
        'fuel_liters',
        'nominal',
        'documentation_photos',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'documentation_photos' => 'array',
            'fuel_liters' => 'decimal:2',
            'nominal' => 'decimal:2',
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

    public function isFuelExpense(): bool
    {
        return $this->expense_type === 'BBM';
    }
}
