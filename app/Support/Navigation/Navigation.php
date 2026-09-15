<?php

namespace App\Support\Navigation;

use App\Enums\MenuItemType;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Support\Localization\LocaleUrls;
use Illuminate\Support\Facades\Cache;

/**
 * Turns the menus managed in the admin panel into a simple array the layout can
 * render, resolved for one locale and cached until an editor changes a menu.
 */
class Navigation
{
    /**
     * @return array<int, array{label: string, url: string|null, target: string, children: array<int, array{label: string, url: string|null, target: string}>}>
     */
    public static function menu(string $key, ?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        return Cache::rememberForever("navigation.{$key}.{$locale}", function () use ($key, $locale): array {
            $menu = Menu::query()->where('key', $key)->first();

            if (! $menu) {
                return [];
            }

            return $menu->rootItems()
                ->where('is_visible', true)
                ->get()
                ->map(fn (MenuItem $item): array => self::item($item, $locale))
                ->filter(fn (array $item): bool => filled($item['label']))
                ->values()
                ->all();
        });
    }

    public static function flushCache(): void
    {
        foreach (['header', 'footer_treatments', 'footer_corporate'] as $key) {
            foreach (array_keys(config('locales.locales', [])) as $locale) {
                Cache::forget("navigation.{$key}.{$locale}");
            }
        }
    }

    /**
     * @return array{label: string, url: string|null, target: string, children: array<int, mixed>}
     */
    private static function item(MenuItem $item, string $locale): array
    {
        return [
            'label' => (string) $item->localized('label', $locale),
            'url' => self::url($item, $locale),
            'target' => $item->target ?? '_self',
            'children' => $item->children
                ->where('is_visible', true)
                ->map(fn (MenuItem $child): array => self::item($child, $locale))
                ->filter(fn (array $child): bool => filled($child['label']))
                ->values()
                ->all(),
        ];
    }

    private static function url(MenuItem $item, string $locale): ?string
    {
        return match ($item->type) {
            MenuItemType::Url => $item->localized('url', $locale),
            MenuItemType::Route => $item->route_name ? route("{$locale}.{$item->route_name}") : null,
            default => $item->linkable ? LocaleUrls::model($item->linkable, $locale) : null,
        };
    }
}
