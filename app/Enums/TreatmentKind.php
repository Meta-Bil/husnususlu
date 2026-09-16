<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TreatmentKind: string implements HasLabel
{
    case PainType = 'pain_type';
    case Procedure = 'procedure';

    public function getLabel(): string
    {
        return match ($this) {
            self::PainType => 'Ağrı türü',
            self::Procedure => 'Girişimsel tedavi',
        };
    }

    /**
     * The `config/locales.php` segment group this kind is served under.
     */
    public function segmentKey(): string
    {
        return match ($this) {
            self::PainType => 'pain_types',
            self::Procedure => 'procedures',
        };
    }

    public function routeName(): string
    {
        return match ($this) {
            self::PainType => 'pain-types.show',
            self::Procedure => 'procedures.show',
        };
    }

    /**
     * Route names use hyphens while segment keys use underscores, so the index
     * route is derived here instead of from `segmentKey()`.
     */
    public function indexRouteName(): string
    {
        return match ($this) {
            self::PainType => 'pain-types.index',
            self::Procedure => 'procedures.index',
        };
    }
}
