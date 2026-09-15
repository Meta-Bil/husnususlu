<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('site.clinic_phone', '+90 216 234 18 81');
        $this->migrator->add('site.whatsapp_phone', '+90 530 171 88 60');
        $this->migrator->add('site.email', 'info@husnususlu.com');
        $this->migrator->add('site.appointment_notification_email', 'info@husnususlu.com');

        $this->migrator->add('site.address', [
            'tr' => 'Göztepe Mah. Bağdat Cad. Alemdar Apt. No:213/3 Kadıköy – İstanbul',
            'en' => 'Göztepe Mah. Bağdat Cad. Alemdar Apt. No:213/3 Kadıköy – Istanbul, Türkiye',
            'ru' => 'Göztepe Mah. Bağdat Cad. Alemdar Apt. No:213/3 Кадыкёй – Стамбул, Турция',
            'ar' => 'حي غوزتبه، شارع بغداد، عمارة ألمدار، رقم 213/3، كاديكوي – إسطنبول',
        ]);

        $this->migrator->add('site.map_url', 'https://maps.app.goo.gl/UcPYLRsWsyWvYPQFA');

        $this->migrator->add('site.working_hours', [
            'tr' => 'Pazartesi – Pazar · 08:00 – 18:00',
            'en' => 'Monday – Sunday · 08:00 – 18:00',
            'ru' => 'Понедельник – воскресенье · 08:00 – 18:00',
            'ar' => 'من الاثنين إلى الأحد · من 08:00 إلى 18:00',
        ]);

        $this->migrator->add('site.socials', [
            'instagram' => 'https://www.instagram.com/husnususlu/',
            'facebook' => 'https://www.facebook.com/DrHusnuSuslu/',
            'youtube' => 'https://www.youtube.com/@ProfDrHusnuSuslu',
        ]);

        $this->migrator->add('site.stats', [
            'experience_years' => 30,
            'patients' => 11700,
            'techniques' => 27,
            'publications' => 28,
        ]);

        $this->migrator->add('site.media_channels', ['NTV', 'Beyaz TV', 'tv100']);

        $this->migrator->add('site.scholar_url', 'https://scholar.google.com/citations?user=eEMcgJkAAAAJ&hl=tr');

        $this->migrator->add('site.doktortakvimi_url', 'https://www.doktortakvimi.com/husnu-suslu/algoloji-anesteziyoloji-ve-reanimasyon/istanbul');
        $this->migrator->add('site.doktortakvimi_doctor_slug', 'husnu-suslu');
        $this->migrator->add('site.doktortakvimi_widget_enabled', false);

        $this->migrator->add('site.medical_disclaimer', [
            'tr' => 'Bu sitedeki içerikler bilgilendirme amaçlıdır; tanı ve tedavi için hekiminize başvurunuz.',
            'en' => 'The content on this site is for information only; consult your physician for diagnosis and treatment.',
            'ru' => 'Информация на сайте носит ознакомительный характер; для диагностики и лечения обратитесь к врачу.',
            'ar' => 'المحتوى في هذا الموقع لأغراض إعلامية فقط؛ يُرجى مراجعة طبيبك للتشخيص والعلاج.',
        ]);

        $this->migrator->add('site.ga_measurement_id', '');
        $this->migrator->add('site.gtm_id', '');
    }
};
