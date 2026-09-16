<?php

namespace Tests\Feature;

use App\Blocks\BlockRegistry;
use App\Blocks\Contracts\Block;
use App\Blocks\RichTextBlock;
use App\Enums\ContentStatus;
use App\Enums\TreatmentKind;
use App\Enums\VideoCategory;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Treatment;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BlocksTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedContent();
    }

    /**
     * @return array<string, array{class-string<Block>}>
     */
    public static function blockProvider(): array
    {
        $cases = [];

        foreach (BlockRegistry::BLOCKS as $block) {
            $cases[$block::key()] = [$block];
        }

        return $cases;
    }

    /**
     * @param  class-string<Block>  $block
     */
    #[DataProvider('blockProvider')]
    public function test_block_has_a_view(string $block): void
    {
        $this->assertTrue(
            View::exists($block::view()),
            "The view [{$block::view()}] of block [{$block::key()}] is missing.",
        );
    }

    /**
     * @param  class-string<Block>  $block
     */
    #[DataProvider('blockProvider')]
    public function test_block_renders_without_data(string $block): void
    {
        $html = view($block::view(), $block::transform([], 'tr'))->render();

        $this->assertIsString($html);
    }

    /**
     * @param  class-string<Block>  $block
     */
    #[DataProvider('blockProvider')]
    public function test_block_renders_with_minimal_data(string $block): void
    {
        $data = static::minimalData()[$block::key()] ?? [];

        $html = view($block::view(), $block::transform($data, 'tr'))->render();

        $this->assertNotSame('', trim($html), "Block [{$block::key()}] rendered nothing for a filled block.");
    }

    /**
     * @param  class-string<Block>  $block
     */
    #[DataProvider('blockProvider')]
    public function test_block_builds_its_admin_schema(string $block): void
    {
        $this->assertNotEmpty($block::schema(), "Block [{$block::key()}] has an empty schema.");
    }

    public function test_registry_exposes_every_block_to_the_page_builder(): void
    {
        $this->assertCount(count(BlockRegistry::BLOCKS), BlockRegistry::builderBlocks());
        $this->assertCount(count(BlockRegistry::BLOCKS), BlockRegistry::map());
    }

    public function test_registry_renders_a_page_of_every_block(): void
    {
        $blocks = array_map(
            fn (string $block): array => ['type' => $block::key(), 'data' => static::minimalData()[$block::key()] ?? []],
            BlockRegistry::BLOCKS,
        );

        $this->assertNotSame('', trim(BlockRegistry::render($blocks, 'tr')->toHtml()));
    }

    public function test_faq_contributes_structured_data(): void
    {
        $graph = BlockRegistry::jsonLd([
            ['type' => 'faq', 'data' => static::minimalData()['faq']],
        ], 'tr');

        $this->assertSame('FAQPage', $graph[0]['@type']);
        $this->assertSame('Nükleoplasti kalıcı mıdır?', $graph[0]['mainEntity'][0]['name']);
    }

    public function test_rich_text_removes_scripts_and_adds_heading_ids(): void
    {
        $rendered = RichTextBlock::transform([
            'show_toc' => true,
            'body' => ['tr' => '<h2>Nükleoplasti nedir?</h2><p>Metin</p><script>alert(1)</script>'],
        ], 'tr');

        $this->assertStringNotContainsString('<script', $rendered['body']);
        $this->assertStringContainsString('id="nukleoplasti-nedir"', $rendered['body']);
        $this->assertSame([['id' => 'nukleoplasti-nedir', 'text' => 'Nükleoplasti nedir?']], $rendered['headings']);
    }

    /**
     * The records the content-driven blocks read from.
     */
    protected function seedContent(): void
    {
        Treatment::create([
            'kind' => TreatmentKind::PainType,
            'title' => ['tr' => 'Bel ve Bacak Ağrıları'],
            'slug' => ['tr' => 'bel-ve-bacak-agrilari'],
            'summary' => ['tr' => 'Bel ve bacağa yayılan ağrılar.'],
            'locales_enabled' => ['tr'],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        Treatment::create([
            'kind' => TreatmentKind::Procedure,
            'title' => ['tr' => 'Nükleoplasti'],
            'slug' => ['tr' => 'nukleoplasti'],
            'summary' => ['tr' => 'Ameliyatsız fıtık tedavisi.'],
            'locales_enabled' => ['tr'],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        Video::create([
            'youtube_id' => 'abcdefghijk',
            'title' => ['tr' => 'Nükleoplasti işlemi nedir?'],
            'description' => ['tr' => 'Kısa bilgilendirme videosu.'],
            'category' => VideoCategory::Info,
            'channel' => 'tv100',
            'program' => ['tr' => 'Biz Bize'],
            'duration' => '1:38',
            'is_visible' => true,
        ]);

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
    }

    /**
     * One filled example per block, so every branch of every view is rendered.
     *
     * @return array<string, array<string, mixed>>
     */
    protected static function minimalData(): array
    {
        $locale = ['tr' => 'Örnek'];

        return [
            'hero' => [
                'variant' => 'facts',
                'title' => ['tr' => 'Ağrısız yaşam'],
                'accent' => ['tr' => 'mümkün.'],
                'facts' => [['value' => ['tr' => '30'], 'label' => ['tr' => 'yıl']]],
            ],
            'stats' => ['use_settings' => false, 'items' => [['value' => '30', 'label' => $locale]]],
            'media_strip' => ['use_settings' => false, 'channels' => [['name' => 'NTV']]],
            'treatment_highlight' => [
                'title' => ['tr' => 'Fıtıkta ameliyat tek çözüm değil.'],
                'lead' => $locale,
                'featured_title' => ['tr' => 'Nükleoplasti'],
                'featured_text' => $locale,
                'facts' => [['value' => ['tr' => '20–30 dk'], 'label' => ['tr' => 'işlem süresi']]],
                'card_source' => 'manual',
                'cards' => [['title' => ['tr' => 'Lazer'], 'text' => $locale, 'url' => '/lazer']],
            ],
            'treatment_index' => [
                'title' => ['tr' => 'Her ağrının bir kaynağı vardır.'],
                'pain_type_ids' => [],
                'show_procedures' => true,
            ],
            'comparison_table' => [
                'title' => ['tr' => 'Lazer tedavisi ve ameliyat'],
                'column_a' => ['tr' => 'Lazer tedavisi'],
                'column_b' => ['tr' => 'Geleneksel ameliyat'],
                'rows' => [['label' => ['tr' => 'Anestezi'], 'value_a' => ['tr' => 'Lokal'], 'value_b' => ['tr' => 'Genel']]],
            ],
            'process_steps' => [
                'title' => ['tr' => 'Tedavi süreciniz'],
                'lead' => $locale,
                'steps' => [['title' => ['tr' => 'Muayene'], 'text' => $locale]],
            ],
            'rich_text' => [
                'background' => 'paper',
                'show_toc' => true,
                'title' => ['tr' => 'Nükleoplasti'],
                'body' => ['tr' => '<h2>Nükleoplasti nedir?</h2><p>Girişimsel bir yöntemdir.</p><ul><li>Kesi yok</li></ul>'],
                'aside' => $locale,
            ],
            'image_text' => [
                'image_side' => 'end',
                'background' => 'navy',
                'title' => ['tr' => 'Kliniğimiz'],
                'body' => ['tr' => '<p>Metin</p>'],
                'link_label' => $locale,
                'link_url' => '/iletisim',
            ],
            'two_column_lists' => [
                'title' => ['tr' => 'Kimlere uygulanır?'],
                'positive_label' => ['tr' => 'Uygulanır'],
                'positive_title' => ['tr' => 'Kimlere uygulanır?'],
                'positive_items' => ['tr' => "Hafif fıtığı olanlar\nAğrısı geçmeyenler"],
                'positive_note' => $locale,
                'negative_label' => ['tr' => 'Uygulanmaz'],
                'negative_title' => ['tr' => 'Kimlere uygulanmaz?'],
                'negative_items' => ['tr' => 'İleri derecede fıtığı olanlar'],
                'negative_note' => $locale,
            ],
            'faq' => [
                'title' => ['tr' => 'Sıkça sorulan sorular'],
                'link_label' => $locale,
                'link_url' => '#randevu',
                'items' => [[
                    'question' => ['tr' => 'Nükleoplasti kalıcı mıdır?'],
                    'answer' => ['tr' => 'Sonuçlar hastadan hastaya değişir.'],
                ]],
            ],
            'video_grid' => ['title' => ['tr' => 'Ekranlarda ağrı bilimi'], 'source' => 'category', 'limit' => 3],
            'latest_posts' => ['title' => ['tr' => 'Bilgi köşesi'], 'source' => 'latest', 'limit' => 3],
            'doctor_profile' => [
                'eyebrow' => ['tr' => 'Hekiminiz'],
                'name' => ['tr' => 'Prof. Dr. Hüsnü Süslü'],
                'text' => $locale,
                'facts' => [['label' => ['tr' => 'Tıp eğitimi'], 'value' => ['tr' => 'Cerrahpaşa']]],
                'primary_label' => ['tr' => 'Özgeçmiş'],
                'primary_url' => '/hakkimda',
            ],
            'timeline' => [
                'title' => ['tr' => 'Eğitim ve kariyer'],
                'lead' => $locale,
                'groups' => [[
                    'label' => ['tr' => 'Eğitim'],
                    'items' => [['year' => '1989 – 1994', 'title' => ['tr' => 'Cerrahpaşa'], 'institution' => $locale]],
                ]],
            ],
            'packages' => [
                'title' => ['tr' => 'Paketler'],
                'lead' => $locale,
                'items' => [[
                    'eyebrow' => ['tr' => 'Başlangıç'],
                    'title' => ['tr' => 'Konaklamasız paket'],
                    'features' => ['tr' => "Muayene\nHastane işlemleri"],
                ]],
            ],
            'related_treatments' => ['title' => ['tr' => 'İlgili tedaviler'], 'source' => 'kind', 'kind' => 'procedure'],
            'publications_link' => [
                'eyebrow' => ['tr' => 'Akademik'],
                'title' => ['tr' => 'Bilimsel yayınlar'],
                'link_label' => ['tr' => 'Google Scholar'],
            ],
            'contact_details' => [
                'title' => ['tr' => 'İletişim'],
                'show_map' => true,
                'map_label' => ['tr' => 'Bağdat Caddesi'],
                'map_area' => ['tr' => 'Kadıköy'],
            ],
            'appointment' => [
                'title' => ['tr' => 'Randevunuzu planlayalım.'],
                'lead' => $locale,
                'show_contact_lines' => true,
            ],
            'cta_band' => [
                'title' => ['tr' => 'Size uygun tedaviyi belirleyelim.'],
                'lead' => $locale,
                'show_whatsapp' => true,
                'show_phone' => true,
            ],
        ];
    }
}
