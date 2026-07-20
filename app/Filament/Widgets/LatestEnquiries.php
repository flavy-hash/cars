<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Models\Enquiry;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * The newest leads, so the dashboard is somewhere you act rather than just look.
 */
class LatestEnquiries extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            // Capped in the query — a dashboard is a glance, not the full list.
            ->query(Enquiry::query()->with('car')->latest()->limit(5))
            ->heading('Latest enquiries')
            ->description('Newest first. Open one to see the full message and contact details.')
            ->paginated(false)
            ->columns([
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->tooltip(fn (Enquiry $record) => $record->created_at->format('j M Y, H:i')),

                TextColumn::make('name')
                    ->label('Buyer')
                    ->weight('semibold')
                    ->description(fn (Enquiry $record) => $record->phone),

                TextColumn::make('car.title')
                    ->label('Car')
                    ->description(fn (Enquiry $record) => $record->car->formatted_price),

                TextColumn::make('type')
                    ->badge(),

                TextColumn::make('status')
                    ->badge(),
            ])
            ->recordUrl(fn (Enquiry $record) => EnquiryResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                // Sent to the resource's view page rather than left as a modal:
                // this table is a widget, not the resource's own, so a modal has
                // no infolist to fill and opens empty.
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('View')
                    ->url(fn (Enquiry $record) => EnquiryResource::getUrl('view', ['record' => $record])),
            ])
            ->headerActions([
                Action::make('allEnquiries')
                    ->label('See all')
                    ->icon('heroicon-m-arrow-right')
                    ->link()
                    ->url(EnquiryResource::getUrl('index')),
            ])
            ->emptyStateHeading('No enquiries yet')
            ->emptyStateDescription('When someone reserves a car or books a test drive, it lands here.');
    }
}
