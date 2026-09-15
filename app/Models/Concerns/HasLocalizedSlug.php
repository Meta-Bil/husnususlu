<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Slugs are per locale. Each table has a generated `slug_{locale}` column so
 * lookups stay indexed instead of scanning the JSON column.
 */
trait HasLocalizedSlug
{
    public function slugFor(?string $locale = null): ?string
    {
        return $this->localized('slug', $locale);
    }

    #[Scope]
    protected function whereSlug(Builder $query, string $slug, ?string $locale = null): Builder
    {
        return $query->where('slug_'.($locale ?? app()->getLocale()), $slug);
    }
}
