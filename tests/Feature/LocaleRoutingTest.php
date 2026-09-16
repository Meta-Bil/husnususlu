<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Enums\PageTemplate;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_turkish_home_page_is_served_without_a_prefix(): void
    {
        $this->createHomePage();

        $this->get('/')
            ->assertOk()
            ->assertSee('Ağrısız yaşam', escape: false);
    }

    public function test_english_home_page_is_served_under_its_prefix(): void
    {
        $this->createHomePage();

        $this->get('/en')
            ->assertOk()
            ->assertSee('A life without pain', escape: false);
    }

    public function test_page_resolves_by_its_slug_in_each_locale(): void
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

        $this->get('/hakkimda')->assertOk()->assertSee('Hakkımda', escape: false);
        $this->get('/en/about-me')->assertOk()->assertSee('About me', escape: false);
        $this->get('/en/hakkimda')->assertNotFound();
    }

    public function test_page_carries_hreflang_alternates_for_every_enabled_locale(): void
    {
        $this->createHomePage();

        $response = $this->get('/');

        $response->assertSee('hreflang="tr"', escape: false);
        $response->assertSee('hreflang="en"', escape: false);
        $response->assertSee('hreflang="x-default"', escape: false);
    }

    public function test_disabled_locale_is_not_routed(): void
    {
        $this->createHomePage();

        $this->get('/ar')->assertNotFound();
    }

    public function test_a_page_hidden_in_a_locale_is_not_reachable_there(): void
    {
        Page::create([
            'template' => PageTemplate::Default,
            'title' => ['tr' => 'Yalnız Türkçe'],
            'slug' => ['tr' => 'yalniz-turkce', 'en' => 'turkish-only'],
            'blocks' => [],
            'locales_enabled' => ['tr'],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $this->get('/yalniz-turkce')->assertOk();
        $this->get('/en/turkish-only')->assertNotFound();
    }

    private function createHomePage(): Page
    {
        return Page::create([
            'template' => PageTemplate::Home,
            'title' => ['tr' => 'Anasayfa', 'en' => 'Home'],
            'slug' => ['tr' => 'anasayfa', 'en' => 'home'],
            'blocks' => [[
                'type' => 'hero',
                'data' => [
                    'variant' => 'home',
                    'title' => ['tr' => 'Ağrısız yaşam', 'en' => 'A life without pain'],
                    'accent' => ['tr' => 'mümkün.', 'en' => 'is possible.'],
                ],
            ]],
            'locales_enabled' => ['tr', 'en'],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ]);
    }
}
