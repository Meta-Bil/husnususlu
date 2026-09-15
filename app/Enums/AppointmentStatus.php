<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AppointmentStatus: string implements HasColor, HasLabel
{
    case New = 'new';
    case Contacted = 'contacted';
    case Scheduled = 'scheduled';
    case Closed = 'closed';

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'Yeni',
            self::Contacted => 'Arandı',
            self::Scheduled => 'Randevu verildi',
            self::Closed => 'Kapatıldı',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'warning',
            self::Contacted => 'info',
            self::Scheduled => 'success',
            self::Closed => 'gray',
        };
    }
}
