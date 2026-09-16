<?php

namespace Tests\Unit\WpImport;

use App\Services\WpImport\Elementor\ChromeFilter;
use App\Services\WpImport\Elementor\ElementorDocument;
use App\Services\WpImport\Elementor\ElementorNode;

/**
 * The `_elementor_data` blobs of six real pages of the old site, dumped into
 * `tests/Fixtures/wp`. They are what the importer actually has to survive, so
 * the parser is tested against them rather than against invented markup.
 */
trait WpFixtures
{
    /** Home, a pain type, the video gallery, a procedure, the publication list, a post. */
    private const FIXTURES = [
        'page-50' => 50,
        'page-374' => 374,
        'page-752' => 752,
        'page-1701' => 1701,
        'page-2258' => 2258,
        'post-2290' => 2290,
    ];

    protected function fixture(string $name): ElementorDocument
    {
        $path = base_path("tests/Fixtures/wp/{$name}.json");

        $this->assertFileExists($path, "Elementor fixture {$name} is missing.");

        return ElementorDocument::fromJson(self::FIXTURES[$name], (string) file_get_contents($path));
    }

    /**
     * @return array<string, ElementorDocument>
     */
    protected function allFixtures(): array
    {
        $documents = [];

        foreach (array_keys(self::FIXTURES) as $name) {
            $documents[$name] = $this->fixture($name);
        }

        return $documents;
    }

    protected function chromeFilter(): ChromeFilter
    {
        return ChromeFilter::fromConfig()->learn($this->allFixtures());
    }

    /**
     * The first widget of a document with a given type.
     */
    protected function widget(ElementorDocument $document, string $widgetType, int $skip = 0): ElementorNode
    {
        foreach ($document->widgets() as $widget) {
            if ($widget->is($widgetType) && $skip-- <= 0) {
                return $widget;
            }
        }

        $this->fail("No {$widgetType} widget in the fixture.");
    }

    /**
     * Pain type records as the importer would have created them, so the mapper
     * can turn the card grids into links without touching the database.
     *
     * @return array<int, array{id: int, kind: string, titles: array<int, string>}>
     */
    protected function painTypeRecords(): array
    {
        $titles = [
            'Baş ve Yüz Ağrıları',
            'Batın ve Pelvik Ağrılar',
            'Bel ve Bacak Ağrıları',
            'Boyun (Servikal) Ağrıları',
            'Fibromiyalji',
            'Kanser Ağrıları',
            'Miyofasiyal Ağrı Sendromu',
            'Nöropatik Ağrılar',
            'Omuz-Kol Ağrıları',
            'Sırt ve Göğüs Ağrıları',
        ];

        return array_map(
            fn (int $index, string $title): array => [
                'id' => $index + 1,
                'kind' => 'pain_type',
                'titles' => [$title],
            ],
            array_keys($titles),
            $titles,
        );
    }
}
