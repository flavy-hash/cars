<?php

namespace App\Filament\Resources\Enquiries\Schemas;

use App\Models\Car;
use App\Models\Enquiry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class EnquiryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Buyer')
                    ->description('Everything you need to pick up the phone.')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name')
                            ->weight('bold')
                            ->size(TextSize::Large),

                        // Copyable so staff can paste straight into a phone or mail client.
                        TextEntry::make('phone')
                            ->icon('heroicon-m-phone')
                            ->copyable()
                            ->url(fn (Enquiry $record) => 'tel:'.preg_replace('/[^+\d]/', '', $record->phone)),

                        TextEntry::make('email')
                            ->icon('heroicon-m-envelope')
                            ->copyable()
                            ->url(fn (Enquiry $record) => 'mailto:'.$record->email),

                        TextEntry::make('message')
                            ->placeholder('No message left.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Request')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('type')
                            ->label('They want to')
                            ->badge(),

                        TextEntry::make('preferred_at')
                            ->label('Preferred date')
                            ->dateTime('l j F Y, H:i')
                            ->placeholder('— (reservations have no date)'),

                        TextEntry::make('status')
                            ->badge(),

                        TextEntry::make('created_at')
                            ->label('Received')
                            ->dateTime('j M Y, H:i')
                            ->since()
                            ->tooltip(fn (Enquiry $record) => $record->created_at->format('l j F Y, H:i')),

                        TextEntry::make('admin_notes')
                            ->label('Internal notes')
                            ->placeholder('None yet.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Car')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        ImageEntry::make('car.image')
                            ->label('Photo')
                            ->disk('public')
                            ->imageHeight(90),

                        TextEntry::make('car.title')
                            ->label('Listing')
                            ->weight('bold')
                            // Straight through to the public page they were looking at.
                            ->url(fn (Enquiry $record) => route('cars.show', $record->car))
                            ->openUrlInNewTab()
                            ->helperText(fn (Enquiry $record) => $record->car->location),

                        TextEntry::make('car.price')
                            ->label('Asking price')
                            ->formatStateUsing(fn ($state) => Car::formatMoney($state)),

                        TextEntry::make('car.status')
                            ->label('Availability')
                            ->badge(),
                    ]),
            ]);
    }
}
