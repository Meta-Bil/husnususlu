<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Enums\MenuItemType;
use App\Enums\PageTemplate;
use App\Enums\TreatmentKind;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Treatment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The Russian edition of the site: how a translated record renders, what it
 * falls back to while a field is still empty, and what `translate:content`
 * does with the reviewed payload in `database/translations`.
 */
class TranslationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_translated_page_renders_in_russian(): void
    {
        $this->createAboutPage();

        $this->get('/ru/obo-mne')
            ->assertOk()
            ->assertSee('lang="ru"', escape: false)
            ->assertSee('Обо мне', escape: false)
            ->assertSee('Жизнь без боли', escape: false)
            ->assertDontSee('Ağrısız yaşam', escape: false);
    }

    public function test_a_field_that_is_still_empty_falls_back_to_turkish(): void
    {
        $this->createAboutPage();

        /* The lead of the hero has no Russian value yet, so the Turkish shows
           through while the rest of the page is already Russian. */
        $this->get('/ru/obo-mne')
            ->assertOk()
            ->assertSee('Otuz yıllık hekimlik deneyimi.', escape: false);
    }

    public function test_the_turkish_page_is_untouched_by_the_russian_one(): void
    {
        $this->createAboutPage();

        $this->get('/hakkimda')
            ->assertOk()
            ->assertSee('Ağrısız yaşam', escape: false)
            ->assertDontSee('Жизнь без боли', escape: false);
    }

    public function test_a_page_without_russian_is_not_served_under_the_russian_prefix(): void
    {
        Page::create([
            'template' => PageTemplate::Default,
            'title' => ['tr' => 'Yalnız Türkçe', 'ru' => 'Только турецкий'],
            'slug' => ['tr' => 'yalniz-turkce', 'ru' => 'tolko-tureckiy'],
            'blocks' => [],
            'locales_enabled' => ['tr'],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $this->get('/ru/tolko-tureckiy')->assertNotFound();
    }

    public function test_the_command_translates_a_record_and_publishes_it_in_the_locale(): void
    {
        $treatment = $this->createFibromyalgia();

        $this->artisan('translate:content', ['--locale' => ['ru']])->assertSuccessful();

        $treatment->refresh();

        $this->assertSame('Фибромиалгия', $treatment->localized('title', 'ru'));
        $this->assertSame('fibromialgiya', $treatment->slugFor('ru'));
        $this->assertTrue($treatment->isEnabledIn('ru'));
        $this->assertSame('Fibromiyalji', $treatment->localized('title', 'tr'), 'The Turkish must not move.');
    }

    public function test_the_command_translates_block_text_by_its_turkish_value(): void
    {
        $page = $this->createAppointmentPage();

        $this->artisan('translate:content', ['--locale' => ['ru']])->assertSuccessful();

        $page->refresh();

        $this->assertSame('Запись на приём', $page->blocks[0]['data']['eyebrow']['ru']);
        $this->assertSame('Randevu', $page->blocks[0]['data']['eyebrow']['tr']);
    }

    public function test_the_command_translates_menu_labels(): void
    {
        $item = MenuItem::create([
            'menu_id' => Menu::create(['key' => 'main', 'name' => 'Ana menü'])->getKey(),
            'label' => ['tr' => 'Hakkımda'],
            'type' => MenuItemType::Page,
        ]);

        $this->artisan('translate:content', ['--locale' => ['ru']])->assertSuccessful();

        $this->assertSame('Обо мне', $item->refresh()->localized('label', 'ru'));
    }

    public function test_a_record_the_payload_only_half_covers_is_not_published(): void
    {
        $treatment = $this->createFibromyalgia();
        $treatment->update(['blocks' => [[
            'type' => 'cta_band',
            'data' => ['title' => ['tr' => 'Çeviri dosyasında olmayan bir başlık']],
        ]]]);

        $this->artisan('translate:content', ['--locale' => ['ru']])->assertSuccessful();

        $treatment->refresh();

        $this->assertSame('Фибромиалгия', $treatment->localized('title', 'ru'), 'The fields it knows are still filled in.');
        $this->assertFalse($treatment->isEnabledIn('ru'), 'A page with untranslated text stays out of the locale.');
    }

    public function test_a_second_run_changes_nothing(): void
    {
        $treatment = $this->createFibromyalgia();

        $this->artisan('translate:content', ['--locale' => ['ru']])->assertSuccessful();

        $first = $this->snapshot($treatment->refresh());

        $this->artisan('translate:content', ['--locale' => ['ru']])->assertSuccessful();

        $this->assertSame($first, $this->snapshot($treatment->refresh()));
    }

    public function test_a_dry_run_writes_nothing(): void
    {
        $treatment = $this->createFibromyalgia();

        $this->artisan('translate:content', ['--locale' => ['ru'], '--dry-run' => true])->assertSuccessful();

        $treatment->refresh();

        $this->assertNull($treatment->localized('title', 'ru', fallback: false));
        $this->assertFalse($treatment->isEnabledIn('ru'));
    }

    public function test_the_disabled_arabic_locale_is_left_alone(): void
    {
        $treatment = $this->createFibromyalgia();

        $this->artisan('translate:content')->assertSuccessful();

        $this->assertNotContains('ar', (array) $treatment->refresh()->locales_enabled);
    }

    /**
     * Everything a second run must leave alone, including the timestamp that
     * only moves when a record is actually saved.
     *
     * @return array<string, mixed>
     */
    private function snapshot(Treatment $treatment): array
    {
        return [
            'title' => $treatment->title,
            'slug' => $treatment->slug,
            'summary' => $treatment->summary,
            'blocks' => $treatment->blocks,
            'locales_enabled' => $treatment->locales_enabled,
            'updated_at' => $treatment->updated_at?->toIso8601String(),
        ];
    }

    /**
     * A page translated the way the payload translates the real ones: Russian
     * everywhere except one block field, which is left empty on purpose.
     */
    private function createAboutPage(): Page
    {
        return Page::create([
            'template' => PageTemplate::Default,
            'title' => ['tr' => 'Hakkımda', 'ru' => 'Обо мне'],
            'slug' => ['tr' => 'hakkimda', 'ru' => 'obo-mne'],
            'blocks' => [[
                'type' => 'hero',
                'data' => [
                    'variant' => 'page',
                    'title' => ['tr' => 'Ağrısız yaşam', 'ru' => 'Жизнь без боли'],
                    'lead' => ['tr' => 'Otuz yıllık hekimlik deneyimi.'],
                ],
            ]],
            'locales_enabled' => ['tr', 'ru'],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ]);
    }

    private function createAppointmentPage(): Page
    {
        return Page::create([
            'template' => PageTemplate::Default,
            'title' => ['tr' => 'İletişim'],
            'slug' => ['tr' => 'iletisim'],
            'blocks' => [[
                'type' => 'appointment',
                'data' => ['eyebrow' => ['tr' => 'Randevu']],
            ]],
            'locales_enabled' => ['tr'],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ]);
    }

    private function createFibromyalgia(): Treatment
    {
        return Treatment::create([
            'kind' => TreatmentKind::PainType,
            'title' => ['tr' => 'Fibromiyalji'],
            'slug' => ['tr' => 'fibromiyalji'],
            'blocks' => [],
            'locales_enabled' => ['tr'],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ]);
    }
}
