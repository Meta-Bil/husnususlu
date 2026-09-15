<?php

namespace App\Models\Concerns;

use App\Support\Localization\Locales;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Translatable fields are stored as JSON maps of locale => value, so one record
 * holds every language and the admin edits them side by side.
 *
 * Models using this trait declare the fields in a `$localized` array.
 */
trait HasLocalizedAttributes
{
    /**
     * @return array<int, string>
     */
    public function localizedAttributes(): array
    {
        return $this->localized ?? [];
    }

    /**
     * Read a translatable field, falling back to the next locale that has content.
     */
    public function localized(string $field, ?string $locale = null, bool $fallback = true): ?string
    {
        $values = $this->getAttribute($field);

        if (is_string($values)) {
            return $values;
        }

        if (! is_array($values)) {
            return null;
        }

        $locale ??= app()->getLocale();

        if (filled($values[$locale] ?? null)) {
            return $values[$locale];
        }

        if (! $fallback) {
            return null;
        }

        foreach (Locales::fallbackChain($locale) as $candidate) {
            if (filled($values[$candidate] ?? null)) {
                return $values[$candidate];
            }
        }

        foreach ($values as $value) {
            if (filled($value)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Whether the record is published in a given locale.
     */
    public function isEnabledIn(?string $locale = null): bool
    {
        $locale ??= app()->getLocale();

        return in_array($locale, (array) ($this->locales_enabled ?? []), true);
    }

    /**
     * Locales the record is published in, limited to the ones the site serves.
     *
     * @return array<int, string>
     */
    public function availableLocales(): array
    {
        return array_values(array_intersect((array) ($this->locales_enabled ?? []), Locales::enabled()));
    }

    #[Scope]
    protected function enabledIn(Builder $query, ?string $locale = null): Builder
    {
        return $query->whereJsonContains('locales_enabled', $locale ?? app()->getLocale());
    }
}
