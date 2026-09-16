<?php

namespace Tests\Unit\WpImport;

use App\Services\WpImport\Elementor\BlockFactory;
use App\Services\WpImport\Elementor\BlockMapper;
use App\Services\WpImport\Elementor\BlockMerger;
use App\Services\WpImport\Elementor\ElementorDocument;
use App\Services\WpImport\Elementor\MappedBlocks;
use App\Services\WpImport\Support\ContentSanitizer;
use App\Services\WpImport\Support\PromotionalClaims;
use App\Services\WpImport\Support\TitleCleaner;
use Tests\TestCase;

class ElementorBlockMapperTest extends TestCase
{
    use WpFixtures;

    public function test_it_reads_the_widget_tree_of_a_real_page(): void
    {
        $document = $this->fixture('page-374');

        $this->assertNotEmpty($document->roots);
        $this->assertCount(18, $document->widgets());
        $this->assertSame('Bel ve Bacak', substr($this->widget($document, 'text-editor')->text(), 0, 12));
    }

    public function test_the_home_slider_becomes_a_hero(): void
    {
        $blocks = $this->map('page-50')->blocks;

        $hero = $this->firstOfType($blocks, 'hero');

        $this->assertSame('home', $hero['data']['variant']);
        $this->assertSame('Prof. Dr. Hüsnü Süslü', $hero['data']['title']['tr']);
        $this->assertStringContainsString('30 Yıllık Hekimlik', $hero['data']['lead']['tr']);
    }

    public function test_the_counter_strip_becomes_one_stats_block_with_the_corrected_publication_count(): void
    {
        $result = $this->map('page-50');
        $stats = $this->firstOfType($result->blocks, 'stats');

        $this->assertCount(1, array_filter($result->blocks, fn (array $b): bool => $b['type'] === 'stats'));
        $this->assertCount(3, $stats['data']['items']);
        $this->assertFalse($stats['data']['use_settings']);

        $values = array_column($stats['data']['items'], 'value');

        $this->assertSame(['11700', '27', '28'], $values, 'The old site advertised 40+ publications.');
        $this->assertContains('Yayın sayısı 40 yerine 28 olarak düzeltildi.', $result->notes);
    }

    public function test_the_pain_type_grid_links_to_treatment_records_instead_of_repeating_their_text(): void
    {
        $index = $this->firstOfType($this->map('page-50')->blocks, 'treatment_index');

        $this->assertCount(10, $this->painTypeRecords());
        $this->assertCount(9, $index['data']['pain_type_ids'], 'Every card on the home grid names a pain type.');
        $this->assertSame(array_unique($index['data']['pain_type_ids']), $index['data']['pain_type_ids']);
    }

    public function test_toggles_become_one_faq_block(): void
    {
        $faq = $this->firstOfType($this->map('page-1701')->blocks, 'faq');

        $this->assertCount(4, $faq['data']['items']);
        $this->assertSame('Nükleoplasti nedir?', $faq['data']['items'][0]['question']['tr']);
        $this->assertStringContainsString('<p>', $faq['data']['items'][0]['answer']['tr']);
        $this->assertStringNotContainsString('data-start', $faq['data']['items'][0]['answer']['tr']);
    }

    public function test_the_playlist_becomes_a_video_grid_of_the_films_the_gallery_holds(): void
    {
        /* Three of the playlist's films, as the video step would have saved them. */
        $videos = ['obBgAWjzUpQ' => 11, 'JLp83nsJ5YA' => 12, 'n-g6h1R2Ga8' => 13];

        $grid = $this->firstOfType($this->mapper('tr')->withVideos($videos)->map($this->fixture('page-752'))->blocks, 'video_grid');

        $this->assertSame('selected', $grid['data']['source']);
        $this->assertSame([11, 12, 13], $grid['data']['video_ids']);
        $this->assertFalse($grid['data']['show_featured'], 'Only a lone film is shown large.');
    }

    public function test_a_video_block_falls_back_to_the_newest_films_when_none_are_in_the_gallery(): void
    {
        $result = $this->map('page-374');
        $grid = $this->firstOfType($result->blocks, 'video_grid');

        $this->assertSame('category', $grid['data']['source']);
        $this->assertArrayNotHasKey('video_ids', $grid['data']);
        $this->assertNotEmpty(array_filter(
            $result->notes,
            fn (string $note): bool => str_contains($note, 'galeride bulunamadı'),
        ));
    }

    public function test_a_single_embedded_film_is_shown_large(): void
    {
        $videos = ['kKRi1ZyHy3o' => 7];

        $blocks = $this->mapper('tr')->withVideos($videos)->map($this->fixture('page-374'))->blocks;
        $grid = $this->firstOfType($blocks, 'video_grid');

        $this->assertSame([7], $grid['data']['video_ids']);
        $this->assertTrue($grid['data']['show_featured']);
    }

    public function test_a_promotional_list_item_is_dropped_and_written_into_the_notes(): void
    {
        $result = $this->map('page-1701');
        $lists = $this->firstOfType($result->blocks, 'two_column_lists');
        $items = $lists['data']['positive_items']['tr'].$lists['data']['negative_items']['tr'];

        $this->assertStringNotContainsString('%98', $items);
        $this->assertStringNotContainsString('AĞRISIZ - ACISIZ', $items);
        $this->assertStringContainsString('HEMEN TABURCU İMKANI', $items);

        $this->assertNotEmpty(array_filter(
            $result->notes,
            fn (string $note): bool => str_contains($note, '%98 BAŞARI İMKANI'),
        ));
    }

    public function test_site_chrome_never_reaches_a_block(): void
    {
        foreach (['page-50', 'page-374', 'page-752', 'page-1701'] as $name) {
            $json = json_encode($this->map($name)->blocks, JSON_UNESCAPED_UNICODE);

            $this->assertStringNotContainsString('Whatsaap İletişim', (string) $json, $name);
            $this->assertStringNotContainsString("Facebook'ta Takip Et", (string) $json, $name);
            $this->assertStringNotContainsString('Adres Bilgisi için Tıklayınız', (string) $json, $name);
        }
    }

    public function test_a_heading_does_not_title_a_block_from_another_section(): void
    {
        $blocks = $this->map('page-374')->blocks;
        $prose = $this->firstOfType($blocks, 'rich_text');

        $this->assertArrayNotHasKey('title', $prose['data']);
        $this->assertStringContainsString('Bel ve bacak ağrıları', $prose['data']['body']['tr']);
    }

    public function test_it_records_the_images_a_page_used(): void
    {
        $this->assertContains(61, $this->map('page-50')->imageIds);
        $this->assertSame([], $this->map('page-752')->imageIds);
    }

    public function test_translations_merge_into_one_block_per_language(): void
    {
        $turkish = $this->map('page-1701', 'tr')->blocks;
        $english = $this->map('page-1701', 'en')->blocks;

        $merged = BlockMerger::merge($turkish, $english);
        $faq = $this->firstOfType($merged['blocks'], 'faq');

        $this->assertSame(0, $merged['unmatched']);
        $this->assertSame(
            $faq['data']['items'][0]['question']['tr'],
            $faq['data']['items'][0]['question']['en'],
            'Both passes read the same fixture, so the leaves hold the same text under two locales.',
        );
    }

    /**
     * @param  array<int, array{type: string, data: array<string, mixed>}>  $blocks
     * @return array{type: string, data: array<string, mixed>}
     */
    private function firstOfType(array $blocks, string $type): array
    {
        foreach ($blocks as $block) {
            if ($block['type'] === $type) {
                return $block;
            }
        }

        $this->fail("No {$type} block was mapped; got: ".implode(', ', array_column($blocks, 'type')));
    }

    private function map(string $fixture, string $locale = 'tr'): MappedBlocks
    {
        return $this->mapper($locale)->map($this->fixture($fixture));
    }

    private function mapper(string $locale): BlockMapper
    {
        return (new BlockMapper(
            new BlockFactory($locale),
            $this->chromeFilter(),
            ContentSanitizer::fromConfig(),
            PromotionalClaims::fromConfig(),
            TitleCleaner::fromConfig(),
        ))->withTreatments($this->painTypeRecords());
    }

    public function test_an_empty_document_maps_to_nothing(): void
    {
        $result = $this->mapper('tr')->map(ElementorDocument::fromArray(0, []));

        $this->assertTrue($result->isEmpty());
        $this->assertSame([], $result->notes);
    }
}
