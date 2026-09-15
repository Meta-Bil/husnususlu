<?php

namespace App\Http\Controllers\Front;

use App\Enums\PageTemplate;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\Seo\Meta;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $page = Page::query()
            ->live()
            ->where('template', PageTemplate::Home)
            ->firstOrFail();

        return view('front.page', Meta::forModel($page) + ['page' => $page]);
    }
}
