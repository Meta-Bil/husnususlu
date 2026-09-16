<?php

namespace App\Http\Controllers\Front;

use App\Enums\PageTemplate;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Treatment;
use App\Support\Localization\Locales;
use App\Support\Localization\LocaleUrls;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * One sitemap for every locale the site actually serves, with the
     * alternates Google expects on a multilingual site.
     */
    public function index(): Response
    {
        $entries = [];

        foreach (Locales::enabled() as $locale) {
            $entries[] = [
                'url' => route("{$locale}.home"),
                'alternates' => LocaleUrls::alternates(null, 'home'),
                'lastmod' => null,
            ];

            foreach (['blog.index', 'videos', 'pain-types.index', 'procedures.index'] as $routeName) {
                $entries[] = [
                    'url' => route("{$locale}.{$routeName}"),
                    'alternates' => LocaleUrls::alternates(null, $routeName),
                    'lastmod' => null,
                ];
            }
        }

        $models = [
            Page::query()->live()->where('template', '!=', PageTemplate::Home)->get(),
            Treatment::query()->live()->get(),
            Post::query()->live()->get(),
            PostCategory::query()->get(),
        ];

        foreach ($models as $collection) {
            foreach ($collection as $model) {
                foreach (Locales::enabled() as $locale) {
                    $url = LocaleUrls::model($model, $locale);

                    if (! $url) {
                        continue;
                    }

                    $entries[] = [
                        'url' => $url,
                        'alternates' => LocaleUrls::alternates($model),
                        'lastmod' => $model->updated_at?->toAtomString(),
                    ];
                }
            }
        }

        return response()
            ->view('front.sitemap', ['entries' => $entries])
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /livewire',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines))->header('Content-Type', 'text/plain');
    }
}
