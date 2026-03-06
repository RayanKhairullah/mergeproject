<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'author',
        'description',
        'category_id',
        'cover_image',
        'file_path',
        'download_count',
    ];

    protected $casts = [
        'download_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return (float) $this->reviews()->avg('rating') ?: 0.0;
    }

    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image && Storage::disk('public')->exists($this->cover_image)) {
            return Storage::disk('public')->url($this->cover_image);
        }

        return asset('images/default-book-cover.svg');
    }

    public function getFileUrlAttribute(): string
    {
        if ($this->file_path && Storage::disk('private')->exists($this->file_path)) {
            return route('books.download', $this);
        }

        return '';
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->orderBy('download_count', 'desc');
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('title', 'like', "%{$term}%")
            ->orWhere('author', 'like', "%{$term}%");
    }

    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($book) {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title);

                // Ensure uniqueness
                $originalSlug = $book->slug;
                $counter = 1;

                while (static::where('slug', $book->slug)->exists()) {
                    $book->slug = $originalSlug.'-'.$counter;
                    $counter++;
                }
            }
        });

        static::updating(function ($book) {
            if ($book->isDirty('title') && empty($book->slug)) {
                $book->slug = Str::slug($book->title);

                // Ensure uniqueness
                $originalSlug = $book->slug;
                $counter = 1;

                while (static::where('slug', $book->slug)->where('id', '!=', $book->id)->exists()) {
                    $book->slug = $originalSlug.'-'.$counter;
                    $counter++;
                }
            }
        });
    }
}
