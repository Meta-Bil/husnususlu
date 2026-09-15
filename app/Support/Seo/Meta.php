<?php

namespace App\Support\Seo;

use App\Support\Localization\LocaleUrls;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Builds the head data every page passes to the layout: title, description,
 * canonical URL and the hreflang alternates.
 */
class Meta
{
    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public static function forModel(Model $model, array $overrides = []): array
    {
        $locale = app()->getLocale();

        $title = $model->localized('seo_title', $locale)
            ?: $model->localized('title', $locale)
            ?: $model->localized('name', $locale);

        $description = $model->localized('seo_description', $locale)
            ?: Str::limit(trim(strip_tags((string) $model->localized('excerpt', $locale))), 155);

        return array_merge([
            'title' => self::title($title),
            'description' => filled($description) ? $description : null,
            'noindex' => (bool) ($model->noindex ?? false),
            'canonical' => LocaleUrls::model($model, $locale),
            'alternates' => LocaleUrls::alternates($model),
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public static function forRoute(string $routeName, string $title, ?string $description = null, array $overrides = []): array
    {
        $locale = app()->getLocale();

        return array_merge([
            'title' => self::title($title),
            'description' => $description,
            'noindex' => false,
            'canonical' => route("{$locale}.{$routeName}"),
            'alternates' => LocaleUrls::alternates(null, $routeName),
        ], $overrides);
    }

    private static function title(?string $title): string
    {
        $siteName = config('app.name');

        if (blank($title)) {
            return $siteName;
        }

        return str_contains($title, $siteName) ? $title : $title.' | '.$siteName;
    }
}
