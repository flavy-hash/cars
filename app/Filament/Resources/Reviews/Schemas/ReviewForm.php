<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Enums\ReviewStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Submitted review')
                    ->description('Written by a visitor. Light edits are fine — do not rewrite their opinion.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Reviewer')
                            ->required()
                            ->maxLength(120),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->prefixIcon('heroicon-m-envelope')
                            ->helperText('Never shown on the storefront.'),

                        Select::make('rating')
                            ->options([5 => '5 stars', 4 => '4 stars', 3 => '3 stars', 2 => '2 stars', 1 => '1 star'])
                            ->required()
                            ->native(false),

                        TextInput::make('context')
                            ->label('What they did')
                            ->maxLength(80)
                            ->placeholder('e.g. Bought a Toyota Harrier'),

                        Textarea::make('body')
                            ->label('Review')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),

                Section::make('Moderation')
                    ->description('Only published reviews appear on the site.')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')
                            ->options(ReviewStatus::class)
                            ->required()
                            ->native(false),
                    ]),
            ]);
    }
}
