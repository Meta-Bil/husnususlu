<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SiteSettings extends Settings
{
    public string $clinic_phone;

    public string $whatsapp_phone;

    public string $email;

    public string $appointment_notification_email;

    /** @var array<string, string> Locale => address */
    public array $address;

    public string $map_url;

    /** @var array<string, string> Locale => opening hours */
    public array $working_hours;

    /** @var array<string, string> Network => profile URL */
    public array $socials;

    /** @var array{experience_years: int, patients: int, techniques: int, publications: int} */
    public array $stats;

    /** @var array<int, string> Channel names shown in the "Ekranlarda" strip */
    public array $media_channels;

    public string $scholar_url;

    public string $doktortakvimi_url;

    public string $doktortakvimi_doctor_slug;

    public bool $doktortakvimi_widget_enabled;

    /** @var array<string, string> Locale => disclaimer */
    public array $medical_disclaimer;

    public string $ga_measurement_id;

    public string $gtm_id;

    public static function group(): string
    {
        return 'site';
    }
}
