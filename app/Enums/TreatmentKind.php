<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TreatmentKind: string implements HasLabel
{
    case PainType = 'pain_type';
    case Procedure = 'procedure';

    /**
     * Also the heading and the breadcrumb of the public index pages, so it is
     * translated rather than hard-coded. The admin panel runs in the default
     * locale and still reads the Turkish.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::PainType => __('front.pain_type'),
            self::Procedure => __('front.procedure'),
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
