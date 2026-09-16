<?php

namespace App\Blocks;

use App\Filament\Support\LocaleTabs;
use App\Settings\SiteSettings;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

/**
 * Every way to reach the practice — phone, WhatsApp, e-mail, address and opening
 * hours from site settings — next to a stylised map panel.
 */
class ContactDetailsBlock extends AbstractBlock
{
    public static function key(): string
    {
        return 'contact_details';
    }

    public static function label(): string
    {
        return 'İletişim bilgileri';
    }

    public static function icon(): string
    {
        return 'heroicon-o-map-pin';
    }

    public static function schema(): array
    {
        return [
            LocaleTabs::make(fn (string $locale): array => [
                TextInput::make("eyebrow.{$locale}")->label('Üst etiket')->maxLength(80),
                Textarea::make("title.{$locale}")->label('Başlık')->rows(2),
                TextInput::make("accent.{$locale}")->label('Vurgulu kelime (altın renkli)')->maxLength(80),
                TextInput::make("map_label.{$locale}")
                    ->label('Harita etiketi')
                    ->helperText('Harita üzerinde görünen semt adı.')
                    ->maxLength(80),
                TextInput::make("map_area.{$locale}")
                    ->label('Harita alt bilgisi')
                    ->maxLength(80),
            ]),

            Toggle::make('show_map')
                ->label('Harita panelini göster')
                ->default(true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function transform(array $data, string $locale): array
    {
        $settings = app(SiteSettings::class);

        return [
            'eyebrow' => static::text($data, 'eyebrow', $locale),
            'title' => static::text($data, 'title', $locale),
            'accent' => static::text($data, 'accent', $locale),
            'clinicPhone' => $settings->clinic_phone,
            'whatsappPhone' => $settings->whatsapp_phone,
            'whatsappUrl' => 'https://wa.me/'.preg_replace('/\D/', '', $settings->whatsapp_phone),
            'email' => $settings->email,
            'address' => $settings->address[$locale] ?? $settings->address[config('locales.default')] ?? null,
            'workingHours' => $settings->working_hours[$locale] ?? $settings->working_hours[config('locales.default')] ?? null,
            'mapUrl' => $settings->map_url,
            'showMap' => (bool) ($data['show_map'] ?? true),
            'mapLabel' => static::text($data, 'map_label', $locale),
            'mapArea' => static::text($data, 'map_area', $locale),
        ];
    }
}
