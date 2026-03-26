<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'division_id',
        'user_id',
        'name',
        'nip',
        'gender',
        'image',
        'position',
        'order',
        'show_in_tree',
        'show_in_table',
        'org_section_id',
        'custom_fields',
    ];

    protected $casts = [
        'show_in_tree' => 'boolean',
        'show_in_table' => 'boolean',
        'custom_fields' => 'array',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Employee::class, 'parent_id')->with(['division', 'children']);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orgSection(): BelongsTo
    {
        return $this->belongsTo(OrgSection::class);
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }

        return $this->gender === 'female'
            ? 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=f472b6&color=fff'
            : 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=3b82f6&color=fff';
    }
}
