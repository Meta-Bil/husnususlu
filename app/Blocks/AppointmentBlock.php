<?php

namespace App\Blocks;

use App\Filament\Support\LocaleTabs;
use App\Settings\SiteSettings;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

/**
 * The appointment section: an invitation and the contact lines on one side, the
 * request form on the other, plus the DoktorTakvimi calendar when it is enabled.
 */
class AppointmentBlock extends AbstractBlock
{
    public static function key(): string
    {
        return 'appointment';
    }

    public static function label(): string
    {
        return 'Randevu bölümü';
    }

    public static function icon(): string
    {
        return 'heroicon-o-calendar-days';
    }

    public static function schema(): array
    {
        return [
            LocaleTabs::make(fn (string $locale): array => [
                TextInput::make("eyebrow.{$locale}")->label('Üst etiket')->maxLength(80),
                Textarea::make("title.{$locale}")->label('Başlık')->rows(2),
                TextInput::make("accent.{$locale}")->label('Vurgulu kelime (altın renkli)')->maxLength(80),
                Textarea::make("lead.{$locale}")->label('Giriş metni')->rows(3),
                Textarea::make("calendar_text.{$locale}")
                    ->label('Online takvim açıklaması')
                    ->rows(2),
            ]),

            Toggle::make('show_contact_lines')
                ->label('Telefon, e-posta ve adres satırları')
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
            'lead' => static::text($data, 'lead', $locale),
            'showContactLines' => (bool) ($data['show_contact_lines'] ?? true),
            'clinicPhone' => $settings->clinic_phone,
            'whatsappPhone' => $settings->whatsapp_phone,
            'email' => $settings->email,
            'address' => $settings->address[$locale] ?? $settings->address[config('locales.default')] ?? null,
            'calendarEnabled' => (bool) $settings->doktortakvimi_widget_enabled,
            'calendarUrl' => $settings->doktortakvimi_url,
            'calendarText' => static::text($data, 'calendar_text', $locale),
        ];
    }
}
