<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Enums\PageTemplate;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_sitemap_lists_published_pages_with_their_alternates(): void
    {
        Page::create([
            'template' => PageTemplate::Default,
            'title' => ['tr' => 'Hakkımda', 'en' => 'About me'],
            'slug' => ['tr' => 'hakkimda', 'en' => 'about-me'],
            'blocks' => [],
            'locales_enabled' => ['tr', 'en'],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee('/hakkimda', escape: false);
        $response->assertSee('/en/about-me', escape: false);
        $response->assertSee('hreflang="en"', escape: false);
    }

    public function test_a_draft_page_is_not_listed(): void
    {
        Page::create([
            'template' => PageTemplate::Default,
            'title' => ['tr' => 'Taslak'],
            'slug' => ['tr' => 'taslak-sayfa'],
            'blocks' => [],
            'locales_enabled' => ['tr'],
            'status' => ContentStatus::Draft,
        ]);

        $this->get('/sitemap.xml')->assertDontSee('taslak-sayfa', escape: false);
    }

    public function test_robots_points_at_the_sitemap_and_hides_the_panel(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('sitemap.xml');
    }
}
