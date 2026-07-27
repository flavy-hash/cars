<?php

namespace App\Models;

use Database\Factories\BrandFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * A marque the dealership sells, managed from the admin panel.
 */
class Brand extends Model
{
    /** @use HasFactory<BrandFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Keep the slug in step with the name; it is only used internally, so
        // there is no public URL to break by regenerating it.
        static::saving(function (Brand $brand): void {
            if (blank($brand->slug) || $brand->isDirty('name')) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return HasMany<Car, $this> */
    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    /** Uploaded logo, or null to fall back to the initials tile. */
    public function getLogoUrlAttribute(): ?string
    {
        return Car::resolveImageUrl($this->logo);
    }

    /** The two-letter tile shown when a brand has no logo. */
    public function getInitialsAttribute(): string
    {
        return Str::upper(Str::substr($this->name, 0, 2));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
