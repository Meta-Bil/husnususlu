<?php

namespace App\Blocks;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

/**
 * A navy band that closes a page: a heading, the gold appointment button and the
 * outlined WhatsApp button.
 */
class CtaBandBlock extends AbstractBlock
{
    public static function key(): string
    {
        return 'cta_band';
    }

    public static function label(): string
    {
        return 'Randevu çağrısı (şerit)';
    }

    public static function icon(): string
    {
        return 'heroicon-o-megaphone';
    }

    public static function schema(): array
    {
        return [
            LocaleTabs::make(fn (string $locale): array => [
                TextInput::make("eyebrow.{$locale}")->label('Üst etiket')->maxLength(80),
                Textarea::make("title.{$locale}")
                    ->label('Başlık')
                    ->rows(2)
                    ->required($locale === config('locales.default')),
                TextInput::make("accent.{$locale}")->label('Vurgulu kelime (altın renkli)')->maxLength(80),
                Textarea::make("lead.{$locale}")->label('Metin')->rows(3),
                TextInput::make("button_label.{$locale}")
                    ->label('Buton metni')
                    ->placeholder(__('front.request_appointment', [], $locale))
                    ->maxLength(60),
            ]),

            TextInput::make('button_url')
                ->label('Buton adresi')
                ->helperText('Boş bırakılırsa sayfadaki randevu bölümüne bağlanır.')
                ->maxLength(255),

            Toggle::make('show_whatsapp')
                ->label('WhatsApp butonu')
                ->default(true),

            Toggle::make('show_phone')
                ->label('Telefon satırı')
                ->default(true),
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
            'buttonLabel' => static::text($data, 'button_label', $locale) ?? __('front.request_appointment', [], $locale),
            'buttonUrl' => ($data['button_url'] ?? null) ?: '#randevu',
            'showWhatsapp' => (bool) ($data['show_whatsapp'] ?? true),
            'showPhone' => (bool) ($data['show_phone'] ?? true),
        ];
    }
}
