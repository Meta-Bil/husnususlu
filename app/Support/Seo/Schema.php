<?php

namespace App\Support\Seo;

use App\Enums\TreatmentKind;
use App\Models\Page;
use App\Models\Post;
use App\Models\Treatment;
use App\Settings\SiteSettings;
use App\Support\Localization\LocaleUrls;
use Illuminate\Support\Str;

/**
 * JSON-LD nodes. Search engines use these to show the practice, its address and
 * the medical pages correctly.
 */
class Schema
{
    /**
     * @return array<string, mixed>
     */
    public static function physician(): array
    {
        $settings = app(SiteSettings::class);
        $locale = app()->getLocale();

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Physician',
            'name' => config('app.name'),
            'medicalSpecialty' => 'PainMedicine',
            'url' => route(app()->getLocale().'.home'),
            'telephone' => $settings->clinic_phone,
            'email' => $settings->email,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $settings->address[$locale] ?? $settings->address['tr'] ?? null,
                'addressLocality' => 'Kadıköy',
                'addressRegion' => 'İstanbul',
                'addressCountry' => 'TR',
            ],
            'openingHours' => $settings->working_hours[$locale] ?? $settings->working_hours['tr'] ?? null,
            'sameAs' => array_values(array_filter([
                $settings->socials['instagram'] ?? null,
                $settings->socials['facebook'] ?? null,
                $settings->socials['youtube'] ?? null,
                $settings->scholar_url ?: null,
                $settings->doktortakvimi_url ?: null,
            ])),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function forPage(Page $page): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => $page->schema_type ?: 'WebPage',
            'name' => $page->localized('title'),
            'description' => $page->localized('seo_description') ?: $page->localized('excerpt'),
            'url' => LocaleUrls::page($page),
            'inLanguage' => app()->getLocale(),
            'isPartOf' => ['@type' => 'WebSite', 'name' => config('app.name')],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function forTreatment(Treatment $treatment): array
    {
        $about = $treatment->kind === TreatmentKind::Procedure
            ? ['@type' => 'MedicalProcedure', 'name' => $treatment->localized('title')]
            : ['@type' => 'MedicalCondition', 'name' => $treatment->localized('title')];

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'MedicalWebPage',
            'name' => $treatment->localized('title'),
            'description' => $treatment->localized('seo_description') ?: $treatment->localized('summary'),
            'url' => LocaleUrls::treatment($treatment),
            'inLanguage' => app()->getLocale(),
            'about' => $about,
            'author' => ['@type' => 'Physician', 'name' => config('app.name')],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function forPost(Post $post): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => Str::limit((string) $post->localized('title'), 110, ''),
            'description' => $post->localized('seo_description') ?: $post->localized('excerpt'),
            'url' => LocaleUrls::post($post),
            'inLanguage' => app()->getLocale(),
            'datePublished' => $post->published_at?->toAtomString(),
            'dateModified' => $post->updated_at?->toAtomString(),
            'author' => ['@type' => 'Person', 'name' => config('app.name')],
            'publisher' => ['@type' => 'Organization', 'name' => config('app.name')],
        ]);
    }

    /**
     * @param  array<int, array{label: string, url?: string|null}>  $items
     * @return array<string, mixed>
     */
    public static function breadcrumbs(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_values(array_map(fn (int $index, array $item): array => array_filter([
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['label'],
                'item' => $item['url'] ?? null,
            ]), array_keys($items), $items)),
        ];
    }
}
