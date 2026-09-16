<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Blocks\BlockRegistry;
use App\Enums\ContentStatus;
use App\Enums\PageTemplate;
use App\Filament\Support\LocaleTabs;
use App\Models\Page;
use App\Support\Localization\Locales;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    /**
     * Schema.org types an editor may pick for a page.
     *
     * @return array<string, string>
     */
    public static function schemaTypes(): array
    {
        return [
            'WebPage' => 'Web sayfası',
            'AboutPage' => 'Hakkımda sayfası',
            'ContactPage' => 'İletişim sayfası',
            'MedicalWebPage' => 'Tıbbi içerik sayfası',
            'FAQPage' => 'Sık sorulan sorular',
            'CollectionPage' => 'Liste sayfası',
        ];
    }

    /**
     * Locale codes with their native names, for the "published in" checkboxes.
     *
     * @return array<string, string>
     */
    public static function localeOptions(): array
    {
        return collect(config('locales.locales', []))
            ->map(fn (array $locale, string $code): string => $locale['native'] ?? strtoupper($code))
            ->all();
    }

    public static function configure(Schema $schema): Schema
    {
        $default = Locales::default();

        return $schema
            ->components([
                Tabs::make('Sayfa')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('İçerik')->schema([
                            LocaleTabs::make(fn (string $locale): array => [
                                TextInput::make("title.{$locale}")
                                    ->label('Başlık')
                                    ->maxLength(255)
                                    ->required($locale === $default)
                                    ->live(onBlur: $locale === $default)
                                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set) use ($locale, $default): void {
                                        if ($locale !== $default || $operation !== 'create' || blank($state)) {
                                            return;
                                        }

                                        $set("slug.{$default}", Str::slug($state));
                                    }),
                                TextInput::make("slug.{$locale}")
                                    ->label('URL adı')
                                    ->helperText('Adres çubuğunda görünen kısım. Türkçe başlıktan otomatik önerilir.')
                                    ->maxLength(255)
                                    ->required($locale === $default),
                                Textarea::make("excerpt.{$locale}")
                                    ->label('Özet')
                                    ->rows(3),
                            ]),
                            Select::make('template')
                                ->label('Şablon')
                                ->options(PageTemplate::class)
                                ->default(PageTemplate::Default->value)
                                ->native(false)
                                ->required(),
                            Select::make('parent_id')
                                ->label('Üst sayfa')
                                ->options(fn (?Page $record): array => static::pageOptions($record))
                                ->searchable()
                                ->placeholder('Yok'),
                        ])->columns(2),

                        Tab::make('Bölümler')->schema([
                            Builder::make('blocks')
                                ->label('Sayfa bölümleri')
                                ->blocks(BlockRegistry::builderBlocks())
                                ->collapsible()
                                ->cloneable()
                                ->blockNumbers(false)
                                ->addActionLabel('Bölüm ekle')
                                ->columnSpanFull(),
                        ]),

                        Tab::make('Görsel')->schema([
                            SpatieMediaLibraryFileUpload::make('cover')
                                ->label('Kapak görseli')
                                ->collection('cover')
                                ->image()
                                ->imageEditor()
                                ->maxSize(5120),
                            SpatieMediaLibraryFileUpload::make('og_image')
                                ->label('Paylaşım görseli')
                                ->helperText('Sosyal medyada paylaşıldığında görünen görsel.')
                                ->collection('og_image')
                                ->image()
                                ->maxSize(5120),
                        ])->columns(2),

                        Tab::make('SEO')->schema([
                            LocaleTabs::make(fn (string $locale): array => [
                                TextInput::make("seo_title.{$locale}")
                                    ->label('SEO başlığı')
                                    ->maxLength(70),
                                Textarea::make("seo_description.{$locale}")
                                    ->label('SEO açıklaması')
                                    ->rows(3)
                                    ->maxLength(180),
                            ], 'Arama motoru metinleri'),
                            Select::make('schema_type')
                                ->label('Schema.org türü')
                                ->options(static::schemaTypes())
                                ->default('WebPage')
                                ->native(false)
                                ->required(),
                            Toggle::make('noindex')
                                ->label('Arama motorlarına kapat')
                                ->helperText('Açıkken sayfa dizine eklenmez ve site haritasına girmez.'),
                        ])->columns(2),

                        Tab::make('Yayın')->schema([
                            Select::make('status')
                                ->label('Durum')
                                ->options(ContentStatus::class)
                                ->default(ContentStatus::Draft->value)
                                ->native(false)
                                ->required(),
                            DateTimePicker::make('published_at')
                                ->label('Yayın tarihi')
                                ->seconds(false)
                                ->helperText('Boş bırakılırsa yayına alındığı anda görünür.'),
                            CheckboxList::make('locales_enabled')
                                ->label('Yayınlanacak diller')
                                ->options(static::localeOptions())
                                ->default([$default])
                                ->columns(2)
                                ->required(),
                            Toggle::make('needs_review')
                                ->label('Gözden geçirilmeli')
                                ->helperText('İçe aktarılan veya çevirisi kontrol bekleyen içerikler için.'),
                        ])->columns(2),
                    ]),
            ]);
    }

    /**
     * Pages that may be chosen as a parent, without the record being edited.
     *
     * @return array<int, string>
     */
    private static function pageOptions(?Page $record): array
    {
        return Page::query()
            ->when($record?->exists, fn ($query) => $query->whereKeyNot($record->getKey()))
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn (Page $page): array => [
                $page->getKey() => $page->localized('title', 'tr') ?? "#{$page->getKey()}",
            ])
            ->all();
    }
}
