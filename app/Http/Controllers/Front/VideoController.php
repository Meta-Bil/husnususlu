<?php

namespace App\Http\Controllers\Front;

use App\Enums\PageTemplate;
use App\Enums\VideoCategory;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Video;
use App\Support\Seo\Meta;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function __invoke(): View
    {
        $page = Page::query()
            ->live()
            ->where('template', PageTemplate::Videos)
            ->first();

        $videos = Video::query()
            ->visible()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get();

        $meta = $page
            ? Meta::forModel($page)
            : Meta::forRoute('videos', __('front.all_videos'));

        return view('front.videos', $meta + [
            'page' => $page,
            'featured' => $videos->firstWhere('is_featured', true) ?? $videos->first(),
            'tvVideos' => $videos->where('category', VideoCategory::Tv)->values(),
            'infoVideos' => $videos->where('category', VideoCategory::Info)->values(),
            'videos' => $videos,
        ]);
    }
}
