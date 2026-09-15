<?php

namespace App\Filament\Pages;

use App\Filament\Support\LocaleTabs;
use App\Settings\SiteSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSiteSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string $settings = SiteSettings::class;

    protected static ?string $navigationLabel = 'Site ayarları';

    protected static ?string $title = 'Site ayarları';

    protected static string|\UnitEnum|null $navigationGroup = 'Site';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Ayarlar')->columnSpanFull()->tabs([
                Tab::make('İletişim')->schema([
                    TextInput::make('clinic_phone')->label('Klinik telefonu')->required(),
                    TextInput::make('whatsapp_phone')->label('WhatsApp / randevu telefonu')->required(),
                    TextInput::make('email')->label('E-posta')->email()->required(),
                    TextInput::make('appointment_notification_email')
                        ->label('Randevu bildirimlerinin gideceği e-posta')
                        ->email()
                        ->required(),
                    TextInput::make('map_url')->label('Google Haritalar bağlantısı')->url(),
                    LocaleTabs::make(fn (string $locale): array => [
                        Textarea::make("address.{$locale}")->label('Adres')->rows(2),
                        TextInput::make("working_hours.{$locale}")->label('Çalışma saatleri'),
                    ], 'Adres ve saatler'),
                ])->columns(2),

                Tab::make('Sayılar')->schema([
                    TextInput::make('stats.experience_years')->label('Hekimlik deneyimi (yıl)')->numeric()->required(),
                    TextInput::make('stats.patients')->label('Hasta sayısı')->numeric()->required(),
                    TextInput::make('stats.techniques')->label('Girişimsel teknik sayısı')->numeric()->required(),
                    TextInput::make('stats.publications')->label('Bilimsel yayın sayısı')->numeric()->required(),
                ])->columns(2),

                Tab::make('Sosyal ve medya')->schema([
                    TextInput::make('socials.instagram')->label('Instagram')->url(),
                    TextInput::make('socials.facebook')->label('Facebook')->url(),
                    TextInput::make('socials.youtube')->label('YouTube')->url(),
                    TextInput::make('scholar_url')->label('Google Scholar profili')->url(),
                    Textarea::make('media_channels')
                        ->label('Ekranlarda görünen kanallar')
                        ->helperText('Her satıra bir kanal adı.')
                        ->rows(3)
                        ->formatStateUsing(fn (?array $state): string => implode("\n", $state ?? []))
                        ->dehydrateStateUsing(fn (?string $state): array => array_values(array_filter(array_map('trim', explode("\n", (string) $state))))),
                ])->columns(2),

                Tab::make('Randevu')->schema([
                    Toggle::make('doktortakvimi_widget_enabled')
                        ->label('DoktorTakvimi takvimini sitede göster')
                        ->helperText('Kapalıyken yalnızca randevu talep formu görünür.'),
                    TextInput::make('doktortakvimi_doctor_slug')->label('DoktorTakvimi profil adı'),
                    TextInput::make('doktortakvimi_url')->label('DoktorTakvimi profil bağlantısı')->url(),
                ])->columns(2),

                Tab::make('Yasal ve ölçüm')->schema([
                    LocaleTabs::make(fn (string $locale): array => [
                        Textarea::make("medical_disclaimer.{$locale}")->label('Tıbbi bilgilendirme notu')->rows(2),
                    ], 'Bilgilendirme notu'),
                    Section::make('Ölçümleme')->schema([
                        TextInput::make('gtm_id')->label('Google Tag Manager kimliği')->placeholder('GTM-XXXXXXX'),
                        TextInput::make('ga_measurement_id')->label('Google Analytics ölçüm kimliği')->placeholder('G-XXXXXXXXXX'),
                    ])->columns(2),
                ]),
            ]),
        ]);
    }
}
