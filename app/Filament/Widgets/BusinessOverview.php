<?php

namespace App\Filament\Widgets;

use App\Enums\CarStatus;
use App\Filament\Resources\Cars\CarResource;
use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Models\Car;
use App\Models\Enquiry;
use App\Support\DailyCounts;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * The four numbers worth glancing at before anything else: is anyone waiting on
 * us, what is left to sell, and what is it worth.
 */
class BusinessOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = Car::count();
        $available = Car::where('status', CarStatus::Available)->count();
        $reserved = Car::where('status', CarStatus::Reserved)->count();
        $sold = Car::where('status', CarStatus::Sold)->count();

        $newLeads = Enquiry::query()->new()->count();
        $lastWeek = DailyCounts::forEnquiries(days: 7);

        return [
            // The only stat that is a to-do list, so it leads and turns amber
            // the moment anything is waiting.
            Stat::make('Leads to answer', $newLeads)
                ->description($newLeads > 0 ? 'Nobody has called these back yet' : 'All caught up')
                ->descriptionIcon($newLeads > 0 ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle')
                ->color($newLeads > 0 ? 'warning' : 'success')
                ->chart(array_values($lastWeek))
                ->chartColor($newLeads > 0 ? 'warning' : 'success')
                ->url(EnquiryResource::getUrl('index')),

            Stat::make('Cars for sale', $available)
                ->description("{$total} listed in total")
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('primary')
                ->url(CarResource::getUrl('index')),

            Stat::make('Reserved', $reserved)
                ->description($sold.' sold all time')
                ->descriptionIcon('heroicon-m-lock-closed')
                ->color($reserved > 0 ? 'warning' : 'gray'),

            Stat::make('Inventory value', Car::formatMoney(Car::where('status', CarStatus::Available)->sum('price')))
                ->description('Asking price of every car still for sale')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
