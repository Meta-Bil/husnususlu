<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum VideoCategory: string implements HasLabel
{
    case Tv = 'tv';
    case Info = 'info';

    public function getLabel(): string
    {
        return match ($this) {
            self::Tv => 'TV programı',
            self::Info => 'Bilgilendirici video',
        };
    }
}
