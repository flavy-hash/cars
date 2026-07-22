<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Enums\ReviewStatus;
use App\Models\Review;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->description(fn (Review $record) => $record->created_at->format('M j, Y H:i'))
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Reviewer')
                    ->searchable()
                    ->weight('semibold')
                    ->description(fn (Review $record) => $record->context ?: '—'),

                TextColumn::make('rating')
                    ->formatStateUsing(fn (int $state) => str_repeat('★', $state).str_repeat('☆', 5 - $state))
                    ->color(fn (int $state) => $state >= 4 ? 'success' : ($state >= 3 ? 'warning' : 'danger'))
                    ->sortable(),

                TextColumn::make('body')
                    ->label('Review')
                    ->limit(70)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
            ])
            // Newest first: this screen is a moderation queue.
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(ReviewStatus::class)
                    ->multiple(),

                SelectFilter::make('rating')
                    ->options([5 => '5 stars', 4 => '4 stars', 3 => '3 stars', 2 => '2 stars', 1 => '1 star']),
            ])
            ->recordActions([
                // One click to publish, so the common case needs no form.
                Action::make('approve')
                    ->label('Publish')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (Review $record) => $record->status !== ReviewStatus::Approved)
                    ->action(function (Review $record) {
                        $record->update(['status' => ReviewStatus::Approved]);

                        Notification::make()->title('Review published')->success()->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-m-x-circle')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalDescription('It stays here for your records but never appears on the site.')
                    ->visible(fn (Review $record) => $record->status !== ReviewStatus::Rejected)
                    ->action(function (Review $record) {
                        $record->update(['status' => ReviewStatus::Rejected]);

                        Notification::make()->title('Review rejected')->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No reviews yet')
            ->emptyStateDescription('When a visitor writes one it lands here for you to publish.');
    }
}
