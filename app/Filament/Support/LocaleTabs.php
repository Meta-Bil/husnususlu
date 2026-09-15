<?php

namespace App\Filament\Support;

use App\Support\Localization\Locales;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

/**
 * Renders one tab per language for translatable fields.
 *
 * The callback receives a locale and returns the fields for it, so every field
 * is bound to `field.{locale}` and the whole record is edited in one form:
 *
 *     LocaleTabs::make(fn (string $locale) => [
 *         TextInput::make("title.{$locale}"),
 *     ])
 */
class LocaleTabs
{
    /**
     * @param  callable(string $locale): array<int, mixed>  $fields
     */
    public static function make(callable $fields, string $label = 'Diller'): Tabs
    {
        $tabs = array_map(function (string $locale) use ($fields): Tab {
            $tab = Tab::make(Locales::short($locale))->schema($fields($locale));

            return Locales::isEnabled($locale) ? $tab : $tab->badge('kapalı');
        }, Locales::codes());

        return Tabs::make($label)
            ->tabs($tabs)
            ->persistTabInQueryString('dil')
            ->columnSpanFull();
    }
}
