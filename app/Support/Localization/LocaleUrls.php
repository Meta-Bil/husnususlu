<?php

namespace App\Support\Localization;

use App\Enums\PageTemplate;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Treatment;
use Illuminate\Database\Eloquent\Model;

/**
 * Builds the URL of a record in a given locale and the alternates used for the
 * language switcher and hreflang tags.
 */
class LocaleUrls
{
    public static function model(Model $model, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        if (! Locales::isEnabled($locale)) {
            return null;
        }

        if (method_exists($model, 'isEnabledIn') && ! $model->isEnabledIn($locale)) {
            return null;
        }

        return match (true) {
            $model instanceof Page => self::page($model, $locale),
            $model instanceof Treatment => self::treatment($model, $locale),
            $model instanceof Post => self::post($model, $locale),
            $model instanceof PostCategory => self::category($model, $locale),
            default => null,
        };
    }

    public static function page(Page $page, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        if ($page->template === PageTemplate::Home) {
            return route("{$locale}.home");
        }

        $slug = $page->slugFor($locale);

        return $slug ? route("{$locale}.page", ['slug' => $slug]) : null;
    }

    public static function treatment(Treatment $treatment, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $slug = $treatment->slugFor($locale);

        return $slug ? route("{$locale}.".$treatment->kind->routeName(), ['slug' => $slug]) : null;
    }

    public static function post(Post $post, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $slug = $post->slugFor($locale);

        return $slug ? route("{$locale}.blog.show", ['slug' => $slug]) : null;
    }

    public static function category(PostCategory $category, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $slug = $category->slugFor($locale);

        return $slug ? route("{$locale}.blog.category", ['slug' => $slug]) : null;
    }

    /**
     * URLs of the same content in every enabled locale.
     *
     * Falls back to a locale's home page when the record is not published in
     * that language, so the switcher never points at a 404.
     *
     * @return array<string, string>
     */
    public static function alternates(?Model $model = null, ?string $routeName = null): array
    {
        $alternates = [];

        foreach (Locales::enabled() as $locale) {
            $url = $model
                ? self::model($model, $locale)
                : ($routeName ? route("{$locale}.{$routeName}") : null);

            if ($url) {
                $alternates[$locale] = $url;
            }
        }

        return $alternates;
    }

    /**
     * Targets for the language switcher: every enabled locale, with the home
     * page as a safe fallback.
     *
     * @return array<string, string>
     */
    public static function switcher(?Model $model = null, ?string $routeName = null): array
    {
        $targets = [];

        foreach (Locales::enabled() as $locale) {
            $targets[$locale] = self::model2($model, $routeName, $locale) ?? route("{$locale}.home");
        }

        return $targets;
    }

    private static function model2(?Model $model, ?string $routeName, string $locale): ?string
    {
        if ($model) {
            return self::model($model, $locale);
        }

        return $routeName ? route("{$locale}.{$routeName}") : null;
    }
}
