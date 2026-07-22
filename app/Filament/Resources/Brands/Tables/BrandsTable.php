<?php

namespace App\Filament\Resources\Brands\Tables;

use App\Models\Brand;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BrandsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->imageHeight(36)
                    ->imageWidth(36)
                    ->placeholder('—'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('cars_count')
                    ->label('Cars')
                    ->counts('cars')
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'primary' : 'gray')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('On storefront')
                    ->boolean()
                    ->falseColor('gray')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                TernaryFilter::make('is_active')->label('On storefront'),
            ])
            ->recordActions([
                EditAction::make(),

                // Deleting a brand would orphan its cars, leaving listings with no
                // marque. Block it and point the admin at the safe alternative.
                DeleteAction::make()
                    ->before(function (Brand $record, DeleteAction $action) {
                        if ($record->cars()->exists()) {
                            Notification::make()
                                ->title("{$record->name} still has cars")
                                ->body('Move those cars to another brand first, or switch this brand off instead of deleting it.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No brands yet')
            ->emptyStateDescription('Add the marques you sell — they power the car form and the storefront filters.');
    }
}
