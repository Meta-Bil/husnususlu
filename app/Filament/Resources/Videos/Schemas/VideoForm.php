<?php

namespace App\Filament\Resources\Videos\Schemas;

use App\Enums\VideoCategory;
use App\Filament\Support\LocaleTabs;
use App\Models\Treatment;
use App\Support\Localization\Locales;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class VideoForm
{
    /**
     * Turns a YouTube link of any shape into its bare video id.
     *
     * Accepts watch, short, embed and shorts URLs, and leaves a value that is
     * already an id untouched.
     */
    public static function youtubeId(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $patterns = [
            '#youtu\.be/([A-Za-z0-9_-]{6,})#i',
            '#[?&]v=([A-Za-z0-9_-]{6,})#i',
            '#youtube\.com/embed/([A-Za-z0-9_-]{6,})#i',
            '#youtube\.com/shorts/([A-Za-z0-9_-]{6,})#i',
            '#youtube\.com/live/([A-Za-z0-9_-]{6,})#i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value, $matches) === 1) {
                return $matches[1];
            }
        }

        return $value;
    }

    public static function configure(Schema $schema): Schema
    {
        $default = Locales::default();

        return $schema
            ->components([
                Section::make('Video')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('youtube_id')
                            ->label('YouTube bağlantısı veya kimliği')
                            ->helperText('Tam bağlantı yapıştırabilirsiniz, video kimliği otomatik çıkarılır.')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (?string $state, Set $set): mixed => $set('youtube_id', static::youtubeId($state)))
                            ->dehydrateStateUsing(fn (?string $state): ?string => static::youtubeId($state))
                            ->unique(ignoreRecord: true),
                        SpatieMediaLibraryFileUpload::make('thumbnail')
                            ->label('Kapak görseli')
                            ->helperText('Boş bırakılırsa YouTube kapak görseli kullanılır.')
                            ->collection('thumbnail')
                            ->image()
                            ->maxSize(5120),
                        LocaleTabs::make(fn (string $locale): array => [
                            TextInput::make("title.{$locale}")
                                ->label('Başlık')
                                ->maxLength(255)
                                ->required($locale === $default),
                            Textarea::make("description.{$locale}")
                                ->label('Açıklama')
                                ->rows(3),
                            TextInput::make("program.{$locale}")
                                ->label('Program adı')
                                ->maxLength(255),
                        ]),
                    ]),

                Section::make('Sınıflandırma')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('category')
                            ->label('Kategori')
                            ->options(VideoCategory::class)
                            ->default(VideoCategory::Info->value)
                            ->native(false)
                            ->required(),
                        Select::make('treatment_id')
                            ->label('İlgili tedavi')
                            ->options(fn (): array => static::treatmentOptions())
                            ->searchable()
                            ->placeholder('Yok'),
                        TextInput::make('channel')
                            ->label('Kanal')
                            ->maxLength(255),
                        TextInput::make('duration')
                            ->label('Süre')
                            ->placeholder('12:34')
                            ->maxLength(255),
                        DatePicker::make('published_on')
                            ->label('Yayın tarihi'),
                        TextInput::make('sort_order')
                            ->label('Sıra')
                            ->integer()
                            ->default(0)
                            ->required(),
                        Toggle::make('is_featured')
                            ->label('Öne çıkar'),
                        Toggle::make('is_visible')
                            ->label('Sitede göster')
                            ->default(true),
                    ]),
            ]);
    }

    /**
     * @return array<int, string>
     */
    private static function treatmentOptions(): array
    {
        return Treatment::query()
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn (Treatment $treatment): array => [
                $treatment->getKey() => $treatment->localized('title', 'tr') ?? "#{$treatment->getKey()}",
            ])
            ->all();
    }
}
