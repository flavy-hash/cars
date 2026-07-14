<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'features' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getLocationAttribute(): string
    {
        return "{$this->city}, {$this->state}";
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$'.number_format($this->price);
    }

    public function getFormattedMileageAttribute(): string
    {
        return number_format($this->mileage).' mi';
    }

    /**
     * Apply the listing filters coming from the search bar / sidebar.
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, fn (Builder $q, string $term) => $q->where(
                fn (Builder $q) => $q
                    ->where('title', 'like', "%{$term}%")
                    ->orWhere('brand', 'like', "%{$term}%")
                    ->orWhere('model', 'like', "%{$term}%")
            ))
            ->when($filters['location'] ?? null, fn (Builder $q, string $city) => $q->where('city', $city))
            ->when($filters['body_type'] ?? null, fn (Builder $q, string $type) => $q->where('body_type', $type))
            ->when($filters['brand'] ?? null, fn (Builder $q, string $brand) => $q->where('brand', $brand))
            ->when($filters['transmission'] ?? null, fn (Builder $q, string $t) => $q->where('transmission', $t))
            ->when($filters['fuel_type'] ?? null, fn (Builder $q, string $f) => $q->where('fuel_type', $f))
            ->when($filters['condition'] ?? null, fn (Builder $q, string $c) => $q->where('condition', $c))
            ->when($filters['min_price'] ?? null, fn (Builder $q, $min) => $q->where('price', '>=', (int) $min))
            ->when($filters['max_price'] ?? null, fn (Builder $q, $max) => $q->where('price', '<=', (int) $max));
    }

    public function scopeSort(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'mileage_asc' => $query->orderBy('mileage'),
            'year_desc' => $query->orderByDesc('year'),
            default => $query->orderByDesc('is_featured')->orderByDesc('created_at'),
        };
    }
}
