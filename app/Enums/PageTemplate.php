<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PageTemplate: string implements HasLabel
{
    case Default = 'default';
    case Home = 'home';
    case Contact = 'contact';
    case Videos = 'videos';
    case BlogIndex = 'blog_index';
    case Landing = 'landing';

    public function getLabel(): string
    {
        return match ($this) {
            self::Default => 'Standart sayfa',
            self::Home => 'Anasayfa',
            self::Contact => 'İletişim ve randevu',
            self::Videos => 'Video galeri',
            self::BlogIndex => 'Blog listesi',
            self::Landing => 'Tanıtım sayfası',
        };
    }
}
