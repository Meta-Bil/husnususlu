<?php

namespace App\Blocks;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Storage;

/**
 * The opening section of a page: eyebrow, large title with a gold accent
 * phrase, lead paragraph, calls to action and either a framed photo or a panel
 * of key facts.
 */
class HeroBlock extends AbstractBlock
{
    public static function key(): string
    {
        return 'hero';
    }

    public static function label(): string
    {
        return 'Üst bölüm (hero)';
    }

    public static function icon(): string
    {
        return 'heroicon-o-photo';
    }

    public static function schema(): array
    {
        return [
            Select::make('variant')
                ->label('Görünüm')
                ->options([
                    'home' => 'Anasayfa (büyük başlık + fotoğraf)',
                    'page' => 'İç sayfa (başlık + fotoğraf)',
                    'facts' => 'İç sayfa (başlık + bilgi kutusu)',
                ])
                ->default('page')
                ->required()
                ->live(),

            LocaleTabs::make(fn (string $locale): array => [
                TextInput::make("eyebrow.{$locale}")
                    ->label('Üst etiket')
                    ->maxLength(80),
                Textarea::make("title.{$locale}")
                    ->label('Başlık')
                    ->rows(2)
                    ->required($locale === config('locales.default')),
                TextInput::make("accent.{$locale}")
                    ->label('Vurgulu kelime (altın renkli)')
                    ->helperText('Başlığın sonuna eklenir.')
                    ->maxLength(80),
                Textarea::make("lead.{$locale}")
                    ->label('Giriş metni')
                    ->rows(3),
            ]),

            Section::make('Görsel ve butonlar')
                ->schema([
                    FileUpload::make('image')
                        ->label('Fotoğraf')
                        ->image()
                        ->disk('public')
                        ->directory('blocks/hero')
                        ->imageEditor()
                        ->visible(fn (callable $get): bool => $get('variant') !== 'facts'),
                    Toggle::make('show_cta')
                        ->label('Randevu ve WhatsApp butonları')
                        ->default(true),
                    Toggle::make('show_phone')
                        ->label('Telefon satırı')
                        ->default(true),
                ])
                ->columns(1),

            Repeater::make('facts')
                ->label('Bilgi kutusu')
                ->schema([
                    LocaleTabs::make(fn (string $locale): array => [
                        TextInput::make("value.{$locale}")->label('Değer')->maxLength(40),
                        TextInput::make("label.{$locale}")->label('Açıklama')->maxLength(60),
                    ]),
                ])
                ->defaultItems(0)
                ->collapsed()
                ->itemLabel(fn (array $state): ?string => $state['value'][config('locales.default')] ?? null)
                ->visible(fn (callable $get): bool => $get('variant') === 'facts'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function transform(array $data, string $locale): array
    {
        return [
            'variant' => $data['variant'] ?? 'page',
            'eyebrow' => static::text($data, 'eyebrow', $locale),
            'title' => static::text($data, 'title', $locale),
            'accent' => static::text($data, 'accent', $locale),
            'lead' => static::text($data, 'lead', $locale),
            'image' => filled($data['image'] ?? null) ? Storage::disk('public')->url($data['image']) : null,
            'showCta' => (bool) ($data['show_cta'] ?? true),
            'showPhone' => (bool) ($data['show_phone'] ?? true),
            'facts' => static::rows($data['facts'] ?? [], ['value', 'label'], $locale),
        ];
    }
}
