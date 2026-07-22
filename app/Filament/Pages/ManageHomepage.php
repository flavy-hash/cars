<?php

namespace App\Filament\Pages;

use App\Models\Homepage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * A settings screen rather than a resource: there is exactly one home page, so
 * a list of records would be meaningless.
 */
class ManageHomepage extends Page
{
    protected string $view = 'filament.pages.manage-homepage';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $title = 'Home page';

    protected static ?string $navigationLabel = 'Home page';

    protected static ?int $navigationSort = 5;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Homepage::current()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Hero')
                    ->description('The banner at the very top of the home page.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Textarea::make('hero_heading')
                            ->label('Headline')
                            ->required()
                            ->rows(2)
                            ->helperText('Each new line becomes a new line on the site.'),

                        Textarea::make('hero_subheading')
                            ->label('Sub-heading')
                            ->required()
                            ->rows(3),

                        TextInput::make('trust_badge')
                            ->label('Trust badge')
                            ->maxLength(60)
                            ->placeholder('Trusted by 25K+')
                            ->helperText('The small pill in the top right. Leave empty to hide it.'),

                        FileUpload::make('hero_image')
                            ->label('Background photo')
                            ->image()
                            ->disk('public')
                            ->directory('homepage')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->afterStateHydrated(self::forgetRemoteUrl())
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->helperText('Wide and dark works best — text sits on top. Leave empty to keep the current one.'),
                    ]),

                Section::make('"More than just a car"')
                    ->description('The band below the hero.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('about_eyebrow')
                            ->label('Small label above the heading')
                            ->maxLength(60),

                        Textarea::make('about_heading')
                            ->label('Heading')
                            ->required()
                            ->rows(2),

                        Textarea::make('about_body')
                            ->label('Paragraph')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Repeater::make('about_points')
                            ->label('Bullet points')
                            ->simple(
                                TextInput::make('point')->required()->maxLength(120),
                            )
                            ->defaultItems(1)
                            ->reorderable()
                            ->addActionLabel('Add a point')
                            ->columnSpanFull(),

                        TextInput::make('stat_label')
                            ->label('Badge label')
                            ->maxLength(60)
                            ->placeholder('Average time to sell'),

                        TextInput::make('stat_value')
                            ->label('Badge value')
                            ->maxLength(30)
                            ->placeholder('9 days'),

                        FileUpload::make('about_image')
                            ->label('Section photo')
                            ->image()
                            ->disk('public')
                            ->directory('homepage')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->afterStateHydrated(self::forgetRemoteUrl())
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->columnSpanFull()
                            ->helperText('Leave empty to keep the current one.'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save changes')
                ->action('save'),

            Action::make('view')
                ->label('View home page')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->color('gray')
                ->url(route('home'))
                ->openUrlInNewTab(),
        ];
    }

    public function save(): void
    {
        Homepage::current()->update($this->form->getState());

        Notification::make()
            ->title('Home page updated')
            ->success()
            ->send();
    }

    /**
     * The seeded photos are remote URLs, which FileUpload cannot represent — it
     * fails validation and blocks the whole form. Clear them so the box shows
     * empty; dehydrated(filled) then keeps the stored value untouched.
     */
    private static function forgetRemoteUrl(): \Closure
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
}
