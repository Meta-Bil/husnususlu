<?php

namespace App\Blocks;

use App\Blocks\Concerns\InteractsWithContent;
use App\Filament\Support\LocaleTabs;
use App\Models\Treatment;
use App\Support\Localization\LocaleUrls;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Storage;

/**
 * One featured procedure — photo, short text and a few facts — followed by three
 * cards. The cards are either treatment records or written by hand.
 */
class TreatmentHighlightBlock extends AbstractBlock
{
    use InteractsWithContent;

    public static function key(): string
    {
        return 'treatment_highlight';
    }

    public static function label(): string
    {
        return 'Öne çıkan tedavi + kartlar';
    }

    public static function icon(): string
    {
        return 'heroicon-o-sparkles';
    }

    public static function schema(): array
    {
        return [
            LocaleTabs::make(fn (string $locale): array => [
                TextInput::make("eyebrow.{$locale}")->label('Üst etiket')->maxLength(80),
                Textarea::make("title.{$locale}")->label('Başlık')->rows(2),
                TextInput::make("accent.{$locale}")->label('Vurgulu kelime (altın renkli)')->maxLength(80),
                Textarea::make("lead.{$locale}")->label('Giriş metni')->rows(3),
            ]),

            Section::make('Öne çıkan işlem')
                ->schema([
                    Select::make('featured_treatment_id')
                        ->label('Tedavi kaydı')
                        ->helperText('Seçilirse başlık, metin ve bağlantı bu kayıttan gelir.')
                        ->options(fn (): array => static::treatmentOptions())
                        ->searchable()
                        ->preload(),
                    FileUpload::make('featured_image')
                        ->label('Fotoğraf')
                        ->image()
                        ->disk('public')
                        ->directory('blocks/treatment-highlight')
                        ->imageEditor(),
                    LocaleTabs::make(fn (string $locale): array => [
                        TextInput::make("featured_eyebrow.{$locale}")->label('Üst etiket')->maxLength(80),
                        TextInput::make("featured_title.{$locale}")->label('Başlık')->maxLength(120),
                        Textarea::make("featured_text.{$locale}")->label('Metin')->rows(3),
                        TextInput::make("featured_link_label.{$locale}")->label('Bağlantı metni')->maxLength(60),
                    ]),
                    TextInput::make('featured_link_url')
                        ->label('Bağlantı adresi')
                        ->helperText('Boş bırakılırsa seçilen tedavi kaydının adresi kullanılır.')
                        ->url()
                        ->maxLength(255),
                    Repeater::make('facts')
                        ->label('Kısa bilgiler')
                        ->schema([
                            LocaleTabs::make(fn (string $locale): array => [
                                TextInput::make("value.{$locale}")->label('Değer')->maxLength(40),
                                TextInput::make("label.{$locale}")->label('Açıklama')->maxLength(60),
                            ]),
                        ])
                        ->defaultItems(0)
                        ->collapsed()
                        ->itemLabel(fn (array $state): ?string => $state['value'][config('locales.default')] ?? null),
                ])
                ->columns(1),

            Section::make('Kartlar')
                ->schema([
                    Select::make('card_source')
                        ->label('Kart kaynağı')
                        ->options([
                            'treatments' => 'Tedavi kayıtları',
                            'manual' => 'Elle yazılan kartlar',
                        ])
                        ->default('treatments')
                        ->required()
                        ->live(),
                    Select::make('card_treatment_ids')
                        ->label('Tedaviler')
                        ->options(fn (): array => static::treatmentOptions())
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->visible(fn (callable $get): bool => $get('card_source') !== 'manual'),
                    Repeater::make('cards')
                        ->label('Kartlar')
                        ->schema([
                            LocaleTabs::make(fn (string $locale): array => [
                                TextInput::make("title.{$locale}")->label('Başlık')->maxLength(120),
                                TextInput::make("eyebrow.{$locale}")->label('Üst etiket')->maxLength(60),
                                Textarea::make("text.{$locale}")->label('Metin')->rows(3),
                                TextInput::make("link_label.{$locale}")->label('Bağlantı metni')->maxLength(60),
                            ]),
                            TextInput::make('url')->label('Bağlantı adresi')->maxLength(255),
                        ])
                        ->defaultItems(0)
                        ->collapsed()
                        ->itemLabel(fn (array $state): ?string => $state['title'][config('locales.default')] ?? null)
                        ->visible(fn (callable $get): bool => $get('card_source') === 'manual'),
                ])
                ->columns(1),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function transform(array $data, string $locale): array
    {
        return [
            'eyebrow' => static::text($data, 'eyebrow', $locale),
            'title' => static::text($data, 'title', $locale),
            'accent' => static::text($data, 'accent', $locale),
            'lead' => static::text($data, 'lead', $locale),
            'featured' => static::featured($data, $locale),
            'cards' => static::cards($data, $locale),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function featured(array $data, string $locale): array
    {
        $treatment = filled($data['featured_treatment_id'] ?? null)
            ? Treatment::find($data['featured_treatment_id'])
            : null;

        return [
            'eyebrow' => static::text($data, 'featured_eyebrow', $locale),
            'title' => static::text($data, 'featured_title', $locale) ?? $treatment?->localized('title', $locale),
            'text' => static::text($data, 'featured_text', $locale) ?? $treatment?->localized('summary', $locale),
            'linkLabel' => static::text($data, 'featured_link_label', $locale) ?? __('front.details', [], $locale),
            'linkUrl' => $data['featured_link_url'] ?? ($treatment ? LocaleUrls::treatment($treatment, $locale) : null),
            'image' => filled($data['featured_image'] ?? null)
                ? Storage::disk('public')->url($data['featured_image'])
                : (($treatment?->getFirstMediaUrl('cover', 'hero') ?: null)),
            'facts' => static::rows($data['facts'] ?? [], ['value', 'label'], $locale),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array<string, mixed>>
     */
    private static function cards(array $data, string $locale): array
    {
        if (($data['card_source'] ?? 'treatments') === 'manual') {
            return array_map(
                fn (array $card): array => [
                    'title' => static::text($card, 'title', $locale),
                    'eyebrow' => static::text($card, 'eyebrow', $locale),
                    'text' => static::text($card, 'text', $locale),
                    'linkLabel' => static::text($card, 'link_label', $locale) ?? __('front.details', [], $locale),
                    'url' => $card['url'] ?? null,
                ],
                $data['cards'] ?? [],
            );
        }

        return static::treatmentsByIds($data['card_treatment_ids'] ?? [])
            ->map(fn (Treatment $treatment): array => static::treatmentCard($treatment, $locale) + [
                'eyebrow' => null,
                'linkLabel' => __('front.details', [], $locale),
            ])
            ->all();
    }
}
