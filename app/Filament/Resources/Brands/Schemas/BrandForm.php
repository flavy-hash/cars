<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Brand')
                    ->description('Brands fill the picker on the car form and the "Browse by Brand" tiles.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            // One row per marque, so the storefront filters stay clean.
                            ->unique(ignoreRecord: true)
                            ->helperText('e.g. "Toyota". Shown exactly as typed.'),

                        Toggle::make('is_active')
                            ->label('Show on the storefront')
                            ->default(true)
                            ->helperText('Turn off to hide the brand without deleting its cars.')
                            ->inline(false),

                        FileUpload::make('logo')
                            ->image()
                            ->disk('public')
                            ->directory('brands')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->columnSpanFull()
                            ->helperText('Optional. Square works best — without one, the first two letters are shown.'),
                    ]),
            ]);
    }
}
