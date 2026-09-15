<?php

namespace App\Support\Localization;

/**
 * Thin accessor around `config/locales.php`.
 */
class Locales
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return config('locales.locales', []);
    }

    /**
     * @return array<int, string>
     */
    public static function codes(): array
    {
        return array_keys(self::all());
    }

    /**
     * Locales that are served to visitors right now.
     *
     * @return array<int, string>
     */
    public static function enabled(): array
    {
        return array_keys(array_filter(self::all(), fn (array $locale): bool => (bool) ($locale['enabled'] ?? false)));
    }

    public static function isEnabled(string $locale): bool
    {
        return in_array($locale, self::enabled(), true);
    }

    public static function default(): string
    {
        return config('locales.default', 'tr');
    }

    /**
     * @return array<string, mixed>
     */
    public static function config(string $locale): array
    {
        return self::all()[$locale] ?? [];
    }

    public static function prefix(string $locale): string
    {
        return (string) (self::config($locale)['prefix'] ?? '');
    }

    public static function direction(?string $locale = null): string
    {
        return (string) (self::config($locale ?? app()->getLocale())['dir'] ?? 'ltr');
    }

    public static function isRtl(?string $locale = null): bool
    {
        return self::direction($locale) === 'rtl';
    }

    public static function native(string $locale): string
    {
        return (string) (self::config($locale)['native'] ?? strtoupper($locale));
    }

    public static function short(string $locale): string
    {
        return (string) (self::config($locale)['short'] ?? strtoupper($locale));
    }

    public static function hreflang(string $locale): string
    {
        return (string) (self::config($locale)['hreflang'] ?? $locale);
    }

    /**
     * A fixed URL segment such as the "agri-turleri" part of a pain type URL.
     */
    public static function segment(string $group, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return (string) (config("locales.segments.{$group}.{$locale}")
            ?? config("locales.segments.{$group}." . self::default())
            ?? $group);
    }

    /**
     * Locales to read a translatable field from, in order, starting with the
     * requested one.
     *
     * @return array<int, string>
     */
    public static function fallbackChain(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        return array_values(array_unique(array_merge(
            [$locale],
            config('locales.fallback_chain', []),
            [self::default()],
        )));
    }
}
