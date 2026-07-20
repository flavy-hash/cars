<?php

namespace App\Filament\Resources\Enquiries\Tables;

use App\Enums\CarStatus;
use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Models\Enquiry;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EnquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->description(fn (Enquiry $record) => $record->created_at->format('M j, Y H:i'))
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Buyer')
                    ->searchable()
                    ->weight('semibold')
                    ->description(fn (Enquiry $record) => $record->phone),

                TextColumn::make('car.title')
                    ->label('Car')
                    ->searchable()
                    ->sortable()
                    ->url(fn (Enquiry $record) => route('cars.show', $record->car))
                    ->openUrlInNewTab()
                    ->description(fn (Enquiry $record) => $record->car->formatted_price),

                TextColumn::make('type')
                    ->badge(),

                TextColumn::make('preferred_at')
                    ->label('Wants to drive')
                    ->dateTime('M j, H:i')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // Newest first: this screen is a to-do list, not an archive.
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(EnquiryStatus::class)
                    ->multiple(),

                SelectFilter::make('type')
                    ->options(EnquiryType::class),

                SelectFilter::make('car')
                    ->relationship('car', 'title')
                    ->searchable()
                    ->preload(),
            ])
            // Clicking the row opens the lead — the common case is "read this",
            // not "edit this".
            ->recordUrl(fn (Enquiry $record) => EnquiryResource::getUrl('view', ['record' => $record]))
            // Icon buttons, because four labelled actions push Delete off the right
            // edge of the row on a 1440px screen. Each carries a tooltip.
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('View'),

                // One click from "they want it" to "it's off the market", so staff
                // do not have to go hunting for the car record to do it by hand.
                Action::make('reserveCar')
                    ->label('Mark car reserved')
                    ->iconButton()
                    ->tooltip('Mark car reserved')
                    ->icon('heroicon-m-lock-closed')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription(fn (Enquiry $record) => "This marks \"{$record->car->title}\" as reserved on the storefront.")
                    ->visible(fn (Enquiry $record) => $record->car->status === CarStatus::Available)
                    ->action(function (Enquiry $record) {
                        $record->car->update(['status' => CarStatus::Reserved]);
                        $record->update(['status' => EnquiryStatus::Contacted]);

                        Notification::make()
                            ->title("{$record->car->title} is now reserved")
                            ->success()
                            ->send();
                    }),

                EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),

                // Confirmation is on by default — a lead is someone's real enquiry,
                // and there is no undo.
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No enquiries yet')
            ->emptyStateDescription('When someone reserves a car or books a test drive, it lands here.');
    }
}
