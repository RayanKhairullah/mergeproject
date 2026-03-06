<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiningVenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function banquets(): HasMany
    {
        return $this->hasMany(Banquet::class, 'venue_id');
    }

    public function activeBanquets(): HasMany
    {
        return $this->banquets()->whereIn('status', ['PUBLISHED', 'PENDING_APPROVAL']);
    }
}
