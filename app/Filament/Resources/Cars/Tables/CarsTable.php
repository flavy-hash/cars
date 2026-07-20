<?php

namespace App\Filament\Resources\Cars\Tables;

use App\Enums\CarStatus;
use App\Filament\Resources\Cars\Schemas\CarForm;
use App\Models\Car;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Seeded cars store a remote URL and uploads store a disk path;
                // ImageColumn passes full URLs through, so both render here.
                ImageColumn::make('image')
                    ->label('Photo')
                    ->disk('public')
                    ->imageHeight(44)
                    ->imageWidth(72),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Car $record) => $record->brand.' · '.$record->year)
                    ->weight('semibold'),

                TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn ($state) => Car::formatMoney($state))
                    ->sortable(),

                TextColumn::make('body_type')
                    ->label('Body')
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Availability')
                    ->badge()
                    ->sortable(),

                TextColumn::make('enquiries_count')
                    ->label('Enquiries')
                    ->counts('enquiries')
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'warning' : 'gray')
                    ->sortable(),

                TextColumn::make('condition')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'New' => 'success',
                        'Certified' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(),

                TextColumn::make('location')
                    ->label('Location')
                    ->getStateUsing(fn (Car $record) => $record->location)
                    ->toggleable(),

                TextColumn::make('mileage')
                    ->numeric()
                    ->suffix(' mi')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('badge')
                    ->placeholder('—')
                    ->toggleable(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    // Not being featured is normal, not an error — keep it quiet.
                    ->falseColor('gray')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('brand')
                    ->options(fn () => Car::query()->distinct()->orderBy('brand')->pluck('brand', 'brand')->all())
                    ->searchable(),

                SelectFilter::make('body_type')
                    ->label('Body type')
                    ->options(array_combine(CarForm::BODY_TYPES, CarForm::BODY_TYPES)),

                SelectFilter::make('condition')
                    ->options(array_combine(CarForm::CONDITIONS, CarForm::CONDITIONS)),

                SelectFilter::make('status')
                    ->label('Availability')
                    ->options(CarStatus::class),

                TernaryFilter::make('is_featured')
                    ->label('Featured'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No cars yet')
            ->emptyStateDescription('Add your first car and it will show up on the storefront straight away.');
    }
}
