<?php

namespace App\Support;

use App\Models\Enquiry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Counts per calendar day, with the empty days filled in.
 *
 * Grouping in SQL only returns days that actually have rows, so a quiet Sunday
 * would silently vanish and shift the line along — the gaps have to be zero-filled.
 */
class DailyCounts
{
    /**
     * @return array<string, int> ['2026-07-17' => 3, …] oldest day first
     */
    public static function forEnquiries(int $days, ?Builder $query = null): array
    {
        $from = Carbon::today()->subDays($days - 1);

        $rows = ($query ?? Enquiry::query())
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $counts = [];

        for ($day = $from->copy(); $day <= Carbon::today(); $day->addDay()) {
            $key = $day->toDateString();
            $counts[$key] = (int) ($rows[$key] ?? 0);
        }

        return $counts;
    }
}
