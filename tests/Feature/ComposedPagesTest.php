<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Enums\PageTemplate;
use App\Enums\TreatmentKind;
use App\Enums\VideoCategory;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Treatment;
use App\Models\Video;
use Database\Seeders\ComposePagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The editorial composition of the imported pages: the designed section order,
 * the imported text that has to survive it and the rerun that has to change
 * nothing.
 */
class ComposedPagesTest extends TestCase
{
    use RefreshDatabase;

    /** The bodies the import left on the home page, keyed by what they cover. */
    private const HOME_TEXTS = [
        'lumbar' => '<p>Ameliyatsız bel fıtığı tedavisinde kullanılan lazer yöntemi, cerrahi müdahale gerektirmeden fıtıklaşan diskin sinir üzerindeki baskısını azaltmayı hedefler.</p>',
        'cervical' => '<p>Ameliyatsız boyun fıtığı tedavisinde lazer yöntemi, cerrahi kesiye gerek kalmadan sinir üzerindeki baskıyı azaltmayı amaçlar.</p>',
        'radiofrequency' => '<p>İnce bir iğneyle fıtık bölgesine ulaşılır ve hedeflenen sinir dokusu ısıtılır.</p>',
        'closing' => '<p><strong>Algoloji (Ağrı Tedavisi) Uygulamaları</strong> sinir üzerindeki baskıyı azaltır.</p>',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedImportedContent();
    }

    public function test_the_home_page_follows_the_section_order_of_the_design(): void
    {
        $this->seed(ComposePagesSeeder::class);

        $this->assertSame([
            'hero',
            'stats',
            'media_strip',
            'treatment_highlight',
            'comparison_table',
            'process_steps',
            'rich_text',
            'rich_text',
            'rich_text',
            'rich_text',
            'treatment_index',
            'doctor_profile',
            'video_grid',
            'latest_posts',
            'appointment',
        ], $this->blockTypes($this->homePage()));
    }

    public function test_the_home_hero_and_stats_read_from_the_design_and_the_settings(): void
    {
        $this->seed(ComposePagesSeeder::class);

        $blocks = collect($this->homePage()->blocks)->keyBy('type');

        $this->assertSame('home', $blocks['hero']['data']['variant']);
        $this->assertSame('Ağrısız yaşam', $blocks['hero']['data']['title']['tr']);
        $this->assertSame('mümkün.', $blocks['hero']['data']['accent']['tr']);
        $this->assertTrue($blocks['stats']['data']['use_settings']);
    }

    public function test_the_composition_keeps_every_imported_text_block(): void
    {
        $this->seed(ComposePagesSeeder::class);

        $bodies = collect($this->homePage()->blocks)
            ->where('type', 'rich_text')
            ->pluck('data.body.tr');

        foreach (self::HOME_TEXTS as $body) {
            $this->assertTrue(
                $bodies->contains($body),
                'The composition dropped an imported text block.',
            );
        }
    }

    public function test_shouting_headings_are_rewritten_in_sentence_case(): void
    {
        $this->seed(ComposePagesSeeder::class);

        $titles = collect($this->homePage()->blocks)
            ->where('type', 'rich_text')
            ->pluck('data.title.tr')
            ->all();

        $this->assertContains('Ameliyatsız bel fıtığı', $titles);
        $this->assertContains('Ameliyatsız boyun fıtığı', $titles);
    }

    public function test_the_contact_page_ends_with_the_appointment_form(): void
    {
        $this->seed(ComposePagesSeeder::class);

        $types = $this->blockTypes(Page::query()->where('slug_tr', 'iletisim')->first());

        $this->assertSame(['hero', 'contact_details', 'appointment'], $types);
    }

    public function test_the_composed_records_leave_review_behind_with_a_note(): void
    {
        $this->seed(ComposePagesSeeder::class);

        $page = $this->homePage();

        $this->assertFalse($page->needs_review);
        $this->assertStringContainsString('Tasarıma göre düzenlendi:', $page->import_notes);
        $this->assertStringContainsString('Slider', $page->import_notes, 'The import notes were replaced instead of appended to.');
    }

    public function test_composing_twice_changes_nothing(): void
    {
        $this->seed(ComposePagesSeeder::class);

        $first = [$this->homePage()->blocks, $this->homePage()->import_notes];

        $this->seed(ComposePagesSeeder::class);

        $this->assertSame($first, [$this->homePage()->blocks, $this->homePage()->import_notes]);
    }

    public function test_the_composed_home_page_renders(): void
    {
        $this->seed(ComposePagesSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('Ağrısız yaşam', escape: false)
            ->assertSee('Lazer tedavisi', escape: false)
            ->assertSee('Tedavi süreciniz', escape: false)
            ->assertSee('Bel ve Bacak Ağrıları', escape: false);
    }

    private function homePage(): Page
    {
        return Page::query()->where('template', PageTemplate::Home)->firstOrFail();
    }

    /**
     * @return array<int, string>
     */
    private function blockTypes(?Page $page): array
    {
        return array_column((array) $page?->blocks, 'type');
    }

    /**
     * The records and the generic blocks the WordPress import leaves behind.
     */
    private function seedImportedContent(): void
    {
        foreach (['Bel ve Bacak Ağrıları' => 'bel-ve-bacak-agrilari', 'Fibromiyalji' => 'fibromiyalji'] as $title => $slug) {
            $this->createTreatment(TreatmentKind::PainType, $title, $slug);
        }

        foreach ([
            'Nükleoplasti İşlemi' => 'nukleoplasti-islemi',
            'Ameliyatsız Lazerle Bel Fıtığı Tedavisi' => 'ameliyatsiz-lazerle-bel-fitigi-tedavisi',
            'Radyofrekans Uygulamaları' => 'radyofrekans-uygulamalari',
        ] as $title => $slug) {
            $this->createTreatment(TreatmentKind::Procedure, $title, $slug);
        }

        foreach (['-AWn5RpcsC0' => 'TV100', 'Br54yed9Xjc' => 'Beyaz TV', 'n-g6h1R2Ga8' => 'NTV'] as $youtubeId => $channel) {
            Video::create([
                'youtube_id' => $youtubeId,
                'title' => ['tr' => $channel.' yayını'],
                'category' => VideoCategory::Tv,
                'channel' => $channel,
                'duration' => '2:44',
                'is_visible' => true,
            ]);
        }

        $category = PostCategory::create([
            'name' => ['tr' => 'Bel fıtığı'],
            'slug' => ['tr' => 'bel-fitigi'],
        ]);

        Post::create([
            'post_category_id' => $category->id,
            'title' => ['tr' => 'Bel fıtığı ayağa vurur mu?'],
            'slug' => ['tr' => 'bel-fitigi-ayaga-vurur-mu'],
            'excerpt' => ['tr' => 'Ayağa yayılan ağrı ve sinir basısı belirtileri.'],
            'locales_enabled' => ['tr'],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        Page::create([
            'template' => PageTemplate::Home,
            'title' => ['tr' => 'Anasayfa'],
            'slug' => ['tr' => 'anasayfa'],
            'locales_enabled' => ['tr'],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
            'needs_review' => true,
            'import_notes' => "Atlandı: nav-menu (sitenin her sayfasında tekrar eden öğe)\nSlider'ın ilk görseli hero olarak alındı, diğer 2 slayt atlandı.",
            'blocks' => [
                ['type' => 'stats', 'data' => ['use_settings' => false, 'items' => [['value' => '11700', 'label' => ['tr' => 'Bizi Tercih Eden + Hasta']]]]],
                ['type' => 'hero', 'data' => ['variant' => 'home', 'title' => ['tr' => 'Prof. Dr. Hüsnü Süslü'], 'lead' => ['tr' => '30 Yıllık Hekimlik Tecrübesi']]],
                ['type' => 'rich_text', 'data' => ['background' => 'paper', 'title' => ['tr' => 'AMELİYATSIZ BEL FITIĞI — LAZER İŞLEMİ'], 'body' => ['tr' => self::HOME_TEXTS['lumbar']]]],
                ['type' => 'rich_text', 'data' => ['background' => 'paper', 'title' => ['tr' => 'AMELİYATSIZ BOYUN FITIĞI — LAZER İŞLEMİ'], 'body' => ['tr' => self::HOME_TEXTS['cervical']]]],
                ['type' => 'rich_text', 'data' => ['background' => 'paper', 'title' => ['tr' => 'RADYOFREKANS İLE FITIK TEDAVİSİ'], 'body' => ['tr' => self::HOME_TEXTS['radiofrequency']]]],
                ['type' => 'treatment_index', 'data' => ['show_procedures' => false]],
                ['type' => 'video_grid', 'data' => ['source' => 'selected', 'video_ids' => [], 'show_featured' => true]],
                ['type' => 'rich_text', 'data' => ['background' => 'paper', 'title' => ['tr' => 'Ameliyatsız Fıtık Tedavisi'], 'body' => ['tr' => self::HOME_TEXTS['closing']]]],
            ],
        ]);

        Page::create([
            'template' => PageTemplate::Contact,
            'title' => ['tr' => 'İletişim'],
            'slug' => ['tr' => 'iletisim'],
            'locales_enabled' => ['tr'],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
            'needs_review' => true,
            'blocks' => [
                ['type' => 'rich_text', 'data' => ['background' => 'paper', 'title' => ['tr' => 'Address'], 'body' => ['tr' => '<ul><li>Göztepe Mah.</li></ul>']]],
            ],
        ]);
    }

    private function createTreatment(TreatmentKind $kind, string $title, string $slug): Treatment
    {
        return Treatment::create([
            'kind' => $kind,
            'title' => ['tr' => $title],
            'slug' => ['tr' => $slug],
            'summary' => ['tr' => 'Kısa açıklama.'],
            'blocks' => [],
            'locales_enabled' => ['tr'],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ]);
    }
}
