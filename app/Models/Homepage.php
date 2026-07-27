<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * The editable copy on the home page — a single row, not a list.
 */
class Homepage extends Model
{
    protected $table = 'homepage';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'about_points' => 'array',
        ];
    }

    /**
     * The one and only record. Created on the fly if the table is somehow empty,
     * so a fresh install never renders a blank page.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'hero_heading' => "Discover Cars\nThat Feel Like Yours",
            'hero_subheading' => 'Find handpicked cars for sale that match your taste and your budget.',
            'about_heading' => "More Than Just\nA Car",
            'about_body' => 'We do more than list cars.',
        ]);
    }

    /** Uploaded path or remote URL — both resolve to something servable. */
    public function getHeroImageUrlAttribute(): ?string
    {
        return Car::resolveImageUrl($this->hero_image);
    }

    public function getAboutImageUrlAttribute(): ?string
    {
        return Car::resolveImageUrl($this->about_image);
    }

    /**
     * @return array<int, string>
     */
    public function getPointsAttribute(): array
    {
        return array_values(array_filter($this->about_points ?? []));
    }
}
