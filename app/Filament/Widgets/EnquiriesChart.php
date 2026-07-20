<?php

namespace App\Filament\Widgets;

use App\Support\DailyCounts;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

/**
 * Are leads coming in, and is that getting better or worse?
 *
 * One series, so there is no legend — the heading names it. One y-axis, integer
 * ticks (half an enquiry is not a thing), and a recessive grid.
 */
class EnquiriesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Enquiries per day';

    protected ?string $description = 'The last 14 days.';

    protected ?string $maxHeight = '220px';

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $counts = DailyCounts::forEnquiries(days: 14);

        return [
            'datasets' => [
                [
                    'label' => 'Enquiries',
                    'data' => array_values($counts),
                    'borderColor' => '#ff4500',
                    // A wash under the line reads as volume without competing with it.
                    'backgroundColor' => 'rgba(255, 69, 0, 0.12)',
                    'borderWidth' => 2,
                    'fill' => true,
                    // Monotone, not a plain spline: a cardinal curve overshoots
                    // between points and would dip below zero on a quiet day,
                    // drawing enquiry counts that never happened.
                    'cubicInterpolationMode' => 'monotone',
                    'pointBackgroundColor' => '#ff4500',
                    'pointRadius' => 3,
                    'pointHoverRadius' => 5,
                ],
            ],
            'labels' => array_map(
                fn (string $day) => Carbon::parse($day)->format('j M'),
                array_keys($counts)
            ),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                // Single series: the heading already says what the line is.
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                    'grid' => ['color' => 'rgba(242, 242, 243, 0.06)'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
