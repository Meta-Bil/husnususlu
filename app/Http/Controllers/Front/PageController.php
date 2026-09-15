<?php

namespace App\Http\Controllers\Front;

use App\Enums\PageTemplate;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\Seo\Meta;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View|RedirectResponse
    {
        $locale = app()->getLocale();

        $page = Page::query()
            ->live()
            ->whereSlug($slug)
            ->firstOrFail();

        /*
         * The video gallery and the blog index have their own routes, so a page
         * carrying those templates only supplies the intro copy.
         */
        return match ($page->template) {
            PageTemplate::Home => redirect()->route("{$locale}.home", status: 301),
            PageTemplate::Videos => redirect()->route("{$locale}.videos", status: 301),
            PageTemplate::BlogIndex => redirect()->route("{$locale}.blog.index", status: 301),
            default => view('front.page', Meta::forModel($page) + ['page' => $page]),
        };
    }
}
