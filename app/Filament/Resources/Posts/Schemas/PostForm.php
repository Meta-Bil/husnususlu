<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Enums\ContentStatus;
use App\Filament\Support\LocaleTabs;
use App\Models\PostCategory;
use App\Support\Localization\Locales;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
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

class PostForm
{
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
                Tabs::make('Yazı')
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
                                RichEditor::make("body.{$locale}")
                                    ->label('Yazı')
                                    ->columnSpanFull(),
                            ]),
                            Select::make('post_category_id')
                                ->label('Kategori')
                                ->options(fn (): array => static::categoryOptions())
                                ->searchable()
                                ->placeholder('Kategorisiz'),
                            TextInput::make('video_youtube_id')
                                ->label('YouTube video kimliği')
                                ->helperText('Yazının başında gösterilecek video. Yalnızca kimlik, örneğin dQw4w9WgXcQ.')
                                ->maxLength(255),
                            TextInput::make('reading_time')
                                ->label('Okuma süresi (dakika)')
                                ->integer()
                                ->minValue(1)
                                ->helperText('Boş bırakılırsa yazı uzunluğundan hesaplanır.'),
                        ])->columns(2),

                        Tab::make('Sık sorulanlar')->schema([
                            Repeater::make('faq')
                                ->label('Sık sorulan sorular')
                                ->schema([
                                    LocaleTabs::make(fn (string $locale): array => [
                                        TextInput::make("question.{$locale}")
                                            ->label('Soru')
                                            ->maxLength(255)
                                            ->required($locale === $default),
                                        Textarea::make("answer.{$locale}")
                                            ->label('Cevap')
                                            ->rows(3)
                                            ->required($locale === $default),
                                    ], 'Soru ve cevap'),
                                ])
                                ->itemLabel(fn (array $state): ?string => data_get($state, "question.{$default}"))
                                ->collapsible()
                                ->cloneable()
                                ->defaultItems(0)
                                ->addActionLabel('Soru ekle')
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
                            Toggle::make('noindex')
                                ->label('Arama motorlarına kapat')
                                ->helperText('Açıkken yazı dizine eklenmez ve site haritasına girmez.'),
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
     * @return array<int, string>
     */
    private static function categoryOptions(): array
    {
        return PostCategory::query()
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn (PostCategory $category): array => [
                $category->getKey() => $category->localized('name', 'tr') ?? "#{$category->getKey()}",
            ])
            ->all();
    }
}
