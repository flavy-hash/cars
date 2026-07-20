<?php

namespace App\Filament\Resources\Cars\Schemas;

use App\Enums\CarStatus;
use App\Models\Car;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Image;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CarForm
{
    /**
     * Fixed vocabularies. These drive the storefront's filter dropdowns, so they
     * stay closed sets — a typo'd "Petrol " would show up there as its own option.
     */
    public const BODY_TYPES = ['SUV', 'Sedan', 'Coupe', 'Hatchback', 'Convertible', 'Wagon', 'Pickup', 'Van'];

    public const CONDITIONS = ['New', 'Used', 'Certified'];

    public const TRANSMISSIONS = ['Automatic', 'Manual'];

    public const FUEL_TYPES = ['Petrol', 'Diesel', 'Electric', 'Hybrid'];

    public const BADGES = ['Featured', 'New', 'Hot Deal'];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Listing')
                    ->description('The headline details buyers see first.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->helperText('e.g. "BMW M4 Competition" — the card and page heading.')
                            ->afterStateUpdated(fn (string $operation, $state, callable $set, callable $get) => static::syncSlug($operation, $set, $get('year'), $state)),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Car::class, 'slug', ignoreRecord: true)
                            ->helperText('The page address, e.g. /cars/2023-bmw-m4-competition.')
                            // On create this fills itself in from year + title. On edit it
                            // is left alone: rewriting it would change the car's public URL
                            // and break any link already pointing at the old one.
                            ->hintIcon('heroicon-m-link')
                            ->hint(fn (string $operation) => $operation === 'create' ? 'Auto-filled from year + title' : 'Changing this changes the public URL'),

                        // Free text so a new marque needs no code change, with a
                        // datalist so existing spellings get reused rather than retyped.
                        TextInput::make('brand')
                            ->required()
                            ->maxLength(255)
                            ->datalist(fn () => Car::query()->distinct()->orderBy('brand')->pluck('brand')->all()),

                        TextInput::make('model')
                            ->required()
                            ->maxLength(255)
                            ->datalist(fn () => Car::query()->distinct()->orderBy('model')->pluck('model')->all()),

                        Select::make('body_type')
                            ->label('Body type')
                            ->options(array_combine(self::BODY_TYPES, self::BODY_TYPES))
                            ->required()
                            ->native(false)
                            ->searchable(),

                        Select::make('condition')
                            ->options(array_combine(self::CONDITIONS, self::CONDITIONS))
                            ->default('Used')
                            ->required()
                            ->native(false),

                        Select::make('status')
                            ->label('Availability')
                            ->options(CarStatus::class)
                            ->default(CarStatus::Available)
                            ->required()
                            ->native(false)
                            ->helperText('Sold closes the enquiry form on the storefront.'),

                        Select::make('badge')
                            ->options(array_combine(self::BADGES, self::BADGES))
                            ->placeholder('No badge')
                            ->native(false)
                            ->helperText('The corner ribbon on the card.'),

                        Toggle::make('is_featured')
                            ->label('Show in Featured Cars')
                            ->helperText('Featured cars appear on the home page.')
                            ->inline(false),
                    ]),

                Section::make('Price & location')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('price')
                            ->label('Price (TZS)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            // The column is an unsigned int, so anything past this
                            // would be rejected by the database with no visible
                            // error — an easy trap when a shilling figure gains a
                            // stray zero. Fail in the form instead.
                            ->maxValue(4294967295)
                            ->validationMessages(['max' => 'That price looks too large — check for an extra zero.'])
                            ->step(100000)
                            ->prefix('TSh')
                            ->helperText('Whole shillings, no commas — e.g. 62000000 for TSh 62,000,000.')
                            // Echo it back formatted so a mistyped zero is obvious.
                            ->live(onBlur: true)
                            ->hint(fn (?string $state) => filled($state) ? Car::formatMoney((int) $state) : null),

                        TextInput::make('city')
                            ->required()
                            ->maxLength(255)
                            ->datalist(fn () => Car::query()->distinct()->orderBy('city')->pluck('city')->all()),

                        TextInput::make('state')
                            ->required()
                            ->maxLength(255)
                            ->datalist(fn () => Car::query()->distinct()->orderBy('state')->pluck('state')->all()),
                    ]),

                Section::make('Specification')
                    ->description('Every field here shows in the spec grid on the detail page.')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue((int) date('Y') + 1)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, callable $set, callable $get) => static::syncSlug($operation, $set, $state, $get('title'))),

                        TextInput::make('mileage')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('mi'),

                        TextInput::make('horsepower')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('hp'),

                        Select::make('transmission')
                            ->options(array_combine(self::TRANSMISSIONS, self::TRANSMISSIONS))
                            ->required()
                            ->native(false),

                        Select::make('fuel_type')
                            ->label('Fuel type')
                            ->options(array_combine(self::FUEL_TYPES, self::FUEL_TYPES))
                            ->required()
                            ->native(false),

                        TextInput::make('seats')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(12),

                        TextInput::make('exterior_color')
                            ->label('Exterior colour')
                            ->required()
                            ->maxLength(255)
                            ->helperText('e.g. "Isle of Man Green"'),
                    ]),

                Section::make('Photos')
                    ->description('Upload a photo to replace what the site is showing. JPG or PNG, up to 5 MB.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        // Demo cars still point at a remote photo URL, which FileUpload
                        // cannot preview — without this the box looks empty and there is
                        // no way to tell what the storefront is actually displaying.
                        Image::make(
                            fn (?Car $record): string => $record?->image_url ?? '',
                            fn (?Car $record): string => $record?->title ?? 'Current photo',
                        )
                            ->imageHeight(160)
                            ->visible(fn (?Car $record): bool => filled($record?->image_url))
                            ->columnSpanFull(),

                        FileUpload::make('image')
                            ->label('Replace main photo')
                            ->image()
                            // Required only when creating: on edit, an empty box
                            // means "keep the photo that is already live".
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->disk('public')
                            ->directory('cars')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->afterStateHydrated(self::forgetRemoteUrls())
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->helperText('Shown on the card and as the hero of the detail page. Landscape works best. Leave empty to keep the current photo.'),

                        FileUpload::make('gallery')
                            ->label('Replace gallery')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->disk('public')
                            ->directory('cars')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->afterStateHydrated(self::forgetRemoteUrls())
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->helperText('Thumbnails beside the main photo. Four looks best; drag to reorder. Leave empty to keep the current set.'),
                    ]),

                Section::make('Description & features')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('description')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        TagsInput::make('features')
                            ->placeholder('Add a feature and press enter')
                            ->helperText('e.g. "Heated Seats" — these become the ticked list on the detail page.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Demo cars store a remote photo URL rather than an uploaded path. FileUpload
     * cannot represent one — it fails validation and blocks the whole form, so a
     * price could not be corrected without also re-uploading the picture.
     *
     * Clearing the field leaves the admin an empty upload box (with the live photo
     * previewed above it); paired with dehydrated(filled), the stored URL survives
     * untouched unless a new file is actually chosen.
     */
    private static function forgetRemoteUrls(): Closure
    {
        return function (FileUpload $component, $state): void {
            foreach (is_array($state) ? $state : array_filter([$state]) as $value) {
                if (is_string($value) && str_starts_with($value, 'http')) {
                    $component->state([]);

                    return;
                }
            }
        };
    }

    /**
     * Keep the slug in step with year + title while a car is being created.
     *
     * Deliberately a no-op on edit: an existing car's slug is its public URL,
     * and silently rewriting it when someone fixes a typo in the title would
     * break every link and bookmark already pointing at that car.
     */
    private static function syncSlug(string $operation, callable $set, mixed $year, mixed $title): void
    {
        if ($operation !== 'create') {
            return;
        }

        $set('slug', Str::slug(trim($year.' '.$title)));
    }
}
