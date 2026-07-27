<?php

namespace App\Models;

use App\Enums\ReviewStatus;
use Database\Factories\ReviewFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Review extends Model
{
    /** @use HasFactory<ReviewFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => ReviewStatus::class,
            'rating' => 'integer',
        ];
    }

    /** Only these are ever rendered on the storefront. */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ReviewStatus::Approved);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ReviewStatus::Pending);
    }

    /** The two-letter avatar shown beside the name. */
    public function getInitialsAttribute(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $word) => Str::upper(Str::substr($word, 0, 1)))
            ->join('');
    }

    /**
     * Average score and total, for the summary on the reviews page.
     *
     * @return array{average: float, total: int}
     */
    public static function summary(): array
    {
        $stats = static::query()->approved()->selectRaw('AVG(rating) as average, COUNT(*) as total')->first();

        return [
            'average' => round((float) ($stats->average ?? 0), 1),
            'total' => (int) ($stats->total ?? 0),
        ];
    }
}
