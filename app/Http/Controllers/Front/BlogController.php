<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Support\Seo\Meta;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        return view('front.blog.index', Meta::forRoute('blog.index', __('front.all_posts')) + [
            'posts' => $this->posts(),
            'categories' => $this->categories(),
            'category' => null,
        ]);
    }

    public function category(string $slug): View
    {
        $category = PostCategory::query()->whereSlug($slug)->firstOrFail();

        return view('front.blog.index', Meta::forModel($category) + [
            'posts' => $this->posts($category),
            'categories' => $this->categories(),
            'category' => $category,
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::query()
            ->live()
            ->with('category')
            ->whereSlug($slug)
            ->firstOrFail();

        $related = Post::query()
            ->live()
            ->where('post_category_id', $post->post_category_id)
            ->whereKeyNot($post->getKey())
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('front.blog.show', Meta::forModel($post) + [
            'post' => $post,
            'related' => $related,
        ]);
    }

    /**
     * @return LengthAwarePaginator<int, Post>
     */
    private function posts(?PostCategory $category = null): LengthAwarePaginator
    {
        return Post::query()
            ->live()
            ->with('category')
            ->when($category, fn ($query) => $query->whereBelongsTo($category))
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, PostCategory>
     */
    private function categories()
    {
        return PostCategory::query()
            ->withCount(['posts' => fn ($query) => $query->live()])
            ->orderBy('sort_order')
            ->get();
    }
}
