<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BanquetStatus;
use App\Enums\GuestType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banquet extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'guest_type',
        'venue_id',
        'estimated_guests',
        'cost',
        'scheduled_at',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'approved_at' => 'datetime',
            'guest_type' => GuestType::class,
            'status' => BanquetStatus::class,
            'estimated_guests' => 'integer',
            'cost' => 'decimal:2',
        ];
    }

    public function diningVenue(): BelongsTo
    {
        return $this->belongsTo(DiningVenue::class, 'venue_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
