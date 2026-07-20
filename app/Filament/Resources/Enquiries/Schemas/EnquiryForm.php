<?php

namespace App\Filament\Resources\Enquiries\Schemas;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EnquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // What the buyer told us. Read-only: staff should not quietly
                // rewrite someone's own words or contact details.
                Section::make('Enquiry')
                    ->description('Submitted by the buyer. Read-only.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('car_id')
                            ->label('Car')
                            ->relationship('car', 'title')
                            ->disabled(),

                        Select::make('type')
                            ->options(EnquiryType::class)
                            ->disabled(),

                        TextInput::make('name')
                            ->label('Buyer')
                            ->disabled(),

                        TextInput::make('phone')
                            ->tel()
                            // A lead is useless if you cannot ring it in one click.
                            ->prefixIcon('heroicon-m-phone')
                            ->disabled(),

                        TextInput::make('email')
                            ->email()
                            ->prefixIcon('heroicon-m-envelope')
                            ->disabled(),

                        TextInput::make('preferred_at')
                            ->label('Preferred date')
                            ->placeholder('—')
                            ->disabled(),

                        Textarea::make('message')
                            ->rows(3)
                            ->placeholder('No message')
                            ->columnSpanFull()
                            ->disabled(),
                    ]),

                Section::make('Follow-up')
                    ->description('The only part staff edit.')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')
                            ->options(EnquiryStatus::class)
                            ->required()
                            ->native(false)
                            ->helperText('Move this along as you work the lead.'),

                        Textarea::make('admin_notes')
                            ->label('Internal notes')
                            ->rows(4)
                            ->helperText('Never shown to the buyer.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
