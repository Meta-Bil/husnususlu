<?php

namespace App\Services\WpImport\Elementor;

use App\Services\WpImport\Support\ContentSanitizer;
use App\Services\WpImport\Support\PromotionalClaims;
use App\Services\WpImport\Support\TitleCleaner;
use App\Services\WpImport\Support\TurkishText;
use App\Services\WpImport\Support\YouTubeUrl;

/**
 * Turns an Elementor page into page-builder blocks.
 *
 * The old pages have no semantic structure at all: they are a flat run of
 * widgets inside nested flex containers. The mapper therefore works on the
 * reading order, drops the furniture, and matches the widget runs that carry
 * meaning onto the blocks the new site renders.
 */
class BlockMapper
{
    /** Widgets that only ever decorate and never carry content of their own. */
    private const IGNORED = ['spacer', 'image-carousel', 'call-to-action', 'google_maps', 'menu-anchor'];

    /** @var array<int, string> */
    private array $notes = [];

    /** @var array<int, int> */
    private array $imageIds = [];

    /** @var array<string, array{id: int, kind: string}> Normalised treatment title => record. */
    private array $treatments = [];

    /** @var array<int, int> Kept widget position => the section it came from. */
    private array $sections = [];

    /** @var array<string, int> YouTube id => video record. */
    private array $videos = [];

    public function __construct(
        private readonly BlockFactory $factory,
        private readonly ChromeFilter $chrome,
        private readonly ContentSanitizer $sanitizer,
        private readonly PromotionalClaims $claims,
        private readonly TitleCleaner $titles,
    ) {}

    /**
     * Treatment records the mapper may link cards to instead of copying their
     * text onto every page that shows the grid.
     *
     * @param  array<int, array{id: int, kind: string, titles: array<int, string>}>  $treatments
     */
    public function withTreatments(array $treatments): self
    {
        foreach ($treatments as $treatment) {
            $record = ['id' => $treatment['id'], 'kind' => $treatment['kind']];

            foreach ($treatment['titles'] as $title) {
                foreach ([$this->titleKey($title), $this->tokenKey($title)] as $key) {
                    if ($key !== '') {
                        $this->treatments[$key] ??= $record;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * The video records the page's players may point at.
     *
     * @param  array<string, int>  $videos  YouTube id => record id
     */
    public function withVideos(array $videos): self
    {
        $this->videos = $videos;

        return $this;
    }

    /**
     * The record a card's title names, if any.
     *
     * @return array{id: int, kind: string}|null
     */
    private function treatmentFor(?string $title): ?array
    {
        if (blank($title)) {
            return null;
        }

        return $this->treatments[$this->titleKey($title)]
            ?? $this->treatments[$this->tokenKey($title)]
            ?? null;
    }

    public function map(ElementorDocument $document): MappedBlocks
    {
        $this->notes = [];
        $this->imageIds = [];
        $this->sections = [];

        $widgets = $this->contentWidgets($document);
        $blocks = [];
        $index = 0;

        while ($index < count($widgets)) {
            $consumed = $this->mapAt($widgets, $index, $blocks === []);

            if ($consumed === null) {
                $this->noteSkipped($widgets[$index], 'eşleşen blok yok');
                $index++;

                continue;
            }

            [$block, $length] = $consumed;

            if ($block !== null) {
                $blocks[] = $block;
            }

            $index += max(1, $length);
        }

        return new MappedBlocks($blocks, $this->notes, array_values(array_unique($this->imageIds)));
    }

    /**
     * Widgets worth looking at: the furniture and the purely decorative are
     * already gone, and what went is written down.
     *
     * The top-level container each widget came from is remembered, because
     * that is the only structure an Elementor page has: a heading may only
     * title what shares its section.
     *
     * @return array<int, ElementorNode>
     */
    private function contentWidgets(ElementorDocument $document): array
    {
        $kept = [];

        foreach ($document->roots as $section => $root) {
            foreach ($root->widgets() as $widget) {
                if (in_array((string) $widget->widgetType, self::IGNORED, true)) {
                    $this->noteSkipped($widget, 'süsleme öğesi');

                    continue;
                }

                if ($reason = $this->chrome->reasonFor($widget)) {
                    $this->noteSkipped($widget, $reason);

                    continue;
                }

                $this->sections[count($kept)] = $section;
                $kept[] = $widget;
            }
        }

        return $kept;
    }

    /**
     * Matches the widget run starting at `$index` onto one block.
     *
     * @param  array<int, ElementorNode>  $widgets
     * @return array{0: array{type: string, data: array<string, mixed>}|null, 1: int}|null
     */
    private function mapAt(array $widgets, int $index, bool $atStart): ?array
    {
        $widget = $widgets[$index];

        return match (true) {
            $widget->is('slides') => $this->mapSlides($widget),
            $widget->is('counter') => $this->mapCounters($widgets, $index),
            $widget->is('toggle', 'nested-accordion', 'elementskit-faq', 'accordion') => $this->mapFaq($widgets, $index),
            $widget->is('price-table') => $this->mapPackages($widgets, $index),
            $widget->is('video', 'video-playlist') => $this->mapVideos($widgets, $index),
            $widget->is('icon-box', 'image-box') => $this->mapCards($widgets, $index),
            $widget->is('icon-list') => $this->mapLists($widgets, $index),
            $widget->is('heading') => $this->mapHeading($widgets, $index, $atStart),
            $widget->is('text-editor') => $this->mapProse($widgets, $index, null, $atStart),
            $widget->is('html') => $this->mapHtml($widget),
            $widget->is('image') => $this->mapImage($widget),
            default => null,
        };
    }

    /**
     * The opening slider becomes the hero. Only the first slide survives: the
     * rest were a carousel of the same promise in different words.
     *
     * @return array{0: array<string, mixed>|null, 1: int}
     */
    private function mapSlides(ElementorNode $widget): array
    {
        $slides = $widget->rows('slides');
        $slide = $slides[0] ?? null;

        if ($slide === null) {
            return [null, 1];
        }

        if (count($slides) > 1) {
            $this->note('Slider\'ın ilk görseli hero olarak alındı, diğer '.(count($slides) - 1).' slayt atlandı.');
        }

        $title = $this->clean((string) ($slide['heading'] ?? ''));
        $lead = $this->clean((string) ($slide['description'] ?? ''));

        if ($this->claims->rejects($lead)) {
            $lead = '';
        }

        $this->rememberImage($slide['background_image']['id'] ?? null);

        return [$this->factory->hero($title ?: null, $lead ?: null, variant: 'home'), 1];
    }

    /**
     * A run of counters is the credentials strip.
     *
     * @param  array<int, ElementorNode>  $widgets
     * @return array{0: array<string, mixed>|null, 1: int}
     */
    private function mapCounters(array $widgets, int $index): array
    {
        /* The strip alternates an icon and a number, so the icons in between
           must not end the run. */
        $length = $this->runLength(
            $widgets,
            $index,
            fn (ElementorNode $node): bool => $node->is('counter'),
            fn (ElementorNode $node): bool => $node->text() === '',
        );

        $items = [];

        for ($i = $index; $i < $index + $length; $i++) {
            $node = $widgets[$i];

            if (! $node->is('counter')) {
                continue;
            }

            $value = $this->clean((string) $node->stringSetting('ending_number'));
            $label = trim($this->clean((string) $node->stringSetting('prefix')).' '.$this->clean((string) $node->stringSetting('suffix')));
            $label = trim(preg_replace('/^\+\s*|\s*\+\s*$/u', '', $label) ?? $label);

            if ($value === '') {
                continue;
            }

            $items[] = ['value' => $this->correctedCounter($value, $label), 'label' => $label ?: null];
        }

        return [$items === [] ? null : $this->factory->stats($items), $length];
    }

    /**
     * The old site advertised 40+ publications; the real figure is lower and
     * is corrected here rather than carried over.
     */
    private function correctedCounter(string $value, string $label): string
    {
        $publications = (int) config('wp-import.corrections.publications', 0);
        $isPublications = str_contains(TurkishText::lower($label), 'yayın')
            || str_contains(TurkishText::lower($label), 'publication');

        if ($publications > 0 && $isPublications && (int) $value !== $publications) {
            $this->note("Yayın sayısı {$value} yerine {$publications} olarak düzeltildi.");

            return (string) $publications;
        }

        return $value;
    }

    /**
     * Consecutive toggles, accordions and FAQ widgets all fold into one block.
     *
     * @param  array<int, ElementorNode>  $widgets
     * @return array{0: array<string, mixed>|null, 1: int}
     */
    private function mapFaq(array $widgets, int $index): array
    {
        $length = $this->runLength(
            $widgets,
            $index,
            fn (ElementorNode $node): bool => $node->is('toggle', 'nested-accordion', 'elementskit-faq', 'accordion'),
        );

        $items = [];

        for ($i = $index; $i < $index + $length; $i++) {
            foreach ($this->faqItems($widgets[$i]) as $item) {
                $items[] = $item;
            }
        }

        $title = $this->titleBefore($widgets, $index);

        return [$items === [] ? null : $this->factory->faq($title, $items), $length];
    }

    /**
     * @return array<int, array{question: string, answer: string}>
     */
    private function faqItems(ElementorNode $widget): array
    {
        $items = [];

        foreach ($widget->rows('tabs') as $tab) {
            $items[] = [
                'question' => $this->clean((string) ($tab['tab_title'] ?? '')),
                'answer' => $this->body((string) ($tab['tab_content'] ?? '')),
            ];
        }

        foreach ($widget->rows('ekit_faq_content_items') as $item) {
            $items[] = [
                'question' => $this->clean((string) ($item['ekit_faq_title'] ?? '')),
                'answer' => $this->body((string) ($item['ekit_faq_content'] ?? '')),
            ];
        }

        /* A nested accordion keeps its questions in `items` and its answers in
           the child containers, paired by position. */
        foreach ($widget->rows('items') as $position => $item) {
            $answer = isset($widget->children[$position])
                ? $this->body($this->innerHtml($widget->children[$position]))
                : '';

            $items[] = [
                'question' => $this->clean((string) ($item['item_title'] ?? '')),
                'answer' => $answer,
            ];
        }

        return array_values(array_filter(
            $items,
            fn (array $item): bool => $item['question'] !== '' && $item['answer'] !== '',
        ));
    }

    /**
     * @param  array<int, ElementorNode>  $widgets
     * @return array{0: array<string, mixed>|null, 1: int}
     */
    private function mapPackages(array $widgets, int $index): array
    {
        $length = $this->runLength($widgets, $index, fn (ElementorNode $node): bool => $node->is('price-table'));
        $packages = [];

        for ($i = $index; $i < $index + $length; $i++) {
            $node = $widgets[$i];
            $features = [];

            foreach ($node->rows('features_list') as $feature) {
                $text = $this->clean((string) ($feature['item_text'] ?? ''));

                if ($text === '' || $this->claims->rejects($text)) {
                    continue;
                }

                $features[] = $text;
            }

            $name = $this->clean((string) $node->stringSetting('heading'));

            if ($name === '' && $features === []) {
                continue;
            }

            $packages[] = [
                'name' => $name,
                'eyebrow' => $this->clean((string) $node->stringSetting('sub_heading')) ?: null,
                'features' => $features,
                'button_label' => $this->clean((string) $node->stringSetting('button_text')) ?: null,
                'button_url' => null,
            ];
        }

        if ($packages === []) {
            return [null, $length];
        }

        $block = $this->factory->packages($this->titleBefore($widgets, $index), $packages);

        return [$this->requireBlock($block), $length];
    }

    /**
     * @param  array<int, ElementorNode>  $widgets
     * @return array{0: array<string, mixed>|null, 1: int}
     */
    private function mapVideos(array $widgets, int $index): array
    {
        $length = $this->runLength($widgets, $index, fn (ElementorNode $node): bool => $node->is('video', 'video-playlist'));
        $ids = [];

        for ($i = $index; $i < $index + $length; $i++) {
            $node = $widgets[$i];

            if ($id = YouTubeUrl::id($node->stringSetting('youtube_url'))) {
                $ids[] = $id;
            }

            foreach ($node->rows('tabs') as $tab) {
                if ($id = YouTubeUrl::id((string) ($tab['youtube_url'] ?? ''))) {
                    $ids[] = $id;
                }
            }
        }

        $ids = array_values(array_unique($ids));

        if ($ids === []) {
            return [null, $length];
        }

        $records = [];

        foreach ($ids as $id) {
            if (isset($this->videos[$id])) {
                $records[] = $this->videos[$id];
            }
        }

        if ($records === []) {
            $this->note(count($ids).' video bağlantısı galeride bulunamadı; blok en yeni videoları gösterecek.');
        } elseif (count($records) < count($ids)) {
            $this->note((count($ids) - count($records)).' video bağlantısı galeride bulunamadı.');
        }

        /* The gallery page lists everything; elsewhere a handful is plenty. */
        if (count($records) > 12) {
            $records = array_slice($records, 0, 12);
        }

        return [$this->factory->videoGrid($this->titleBefore($widgets, $index), $records), $length];
    }

    /**
     * Two or more cards in a row. When their titles are pain types or
     * procedures the block links to those records instead of repeating their
     * text on every page that shows the grid.
     *
     * @param  array<int, ElementorNode>  $widgets
     * @return array{0: array<string, mixed>|null, 1: int}
     */
    private function mapCards(array $widgets, int $index): array
    {
        $length = $this->runLength($widgets, $index, fn (ElementorNode $node): bool => $node->is('icon-box', 'image-box'));
        $cards = [];

        for ($i = $index; $i < $index + $length; $i++) {
            $node = $widgets[$i];
            $title = $this->clean((string) $node->stringSetting('title_text'));
            $text = $this->clean((string) $node->stringSetting('description_text'));

            if ($title === '' && $text === '') {
                continue;
            }

            $this->rememberImage($node->imageId());
            $cards[] = ['title' => $title ?: null, 'text' => $this->claims->rejects($text) ? null : ($text ?: null)];
        }

        if ($cards === []) {
            return [null, $length];
        }

        if (count($cards) < 2) {
            return $this->mapSingleCard($widgets, $index, $length, $cards[0]);
        }

        $heading = $this->titleBefore($widgets, $index);
        $real = array_values(array_filter($cards, $this->isRealCard(...)));

        if (count($real) < 2) {
            $this->note('Kart olarak değil süsleme olarak kullanılan '.count($cards).' rozet atlandı.');

            return [null, $length];
        }

        if ($block = $this->treatmentIndexFor($real, $heading)) {
            return [$block, $length];
        }

        return [$this->factory->treatmentHighlight($heading, null, $real), $length];
    }

    /**
     * Elementor's icon boxes were used both for content cards and for little
     * badges — a rating, a name under a portrait. A card has a title and
     * something to say, or names a treatment the site now has a record for.
     *
     * @param  array{title: string|null, text: string|null}  $card
     */
    private function isRealCard(array $card): bool
    {
        if (blank($card['title'])) {
            return false;
        }

        if ($this->treatmentFor($card['title']) !== null) {
            return true;
        }

        return mb_strlen((string) $card['text'], 'UTF-8') >= 25;
    }

    /**
     * A lone card is a label for the text underneath it rather than a card in
     * its own right, so it becomes that text's heading.
     *
     * @param  array<int, ElementorNode>  $widgets
     * @param  array{title: string|null, text: string|null}  $card
     * @return array{0: array<string, mixed>|null, 1: int}
     */
    private function mapSingleCard(array $widgets, int $index, int $length, array $card): array
    {
        $title = trim(implode(' — ', array_filter([$card['title'], $card['text']])));
        $next = $widgets[$index + $length] ?? null;

        if ($next !== null && $next->is('text-editor')) {
            $mapped = $this->mapProse($widgets, $index + $length, $title ?: null, false);

            return [$mapped[0], $length + $mapped[1]];
        }

        $body = filled($card['text']) ? '<p>'.e($card['text']).'</p>' : '';

        return [$body === '' ? null : $this->factory->richText($card['title'], $body), $length];
    }

    /**
     * @param  array<int, array{title: string|null, text: string|null}>  $cards
     * @return array{type: string, data: array<string, mixed>}|null
     */
    private function treatmentIndexFor(array $cards, ?string $heading): ?array
    {
        if ($this->treatments === []) {
            return null;
        }

        $painTypes = [];
        $procedures = [];
        $matched = 0;

        foreach ($cards as $card) {
            $record = $this->treatmentFor($card['title']);

            if ($record === null) {
                continue;
            }

            $matched++;

            if ($record['kind'] === 'pain_type') {
                $painTypes[] = $record['id'];
            } else {
                $procedures[] = $record['id'];
            }
        }

        if ($matched / count($cards) < 0.6) {
            return null;
        }

        if ($matched < count($cards)) {
            $this->note('Kart listesindeki '.(count($cards) - $matched).' başlık bir tedavi kaydıyla eşleşmedi.');
        }

        return $this->factory->treatmentIndex(
            $heading,
            null,
            array_values(array_unique($painTypes)),
            array_values(array_unique($procedures)),
        );
    }

    /**
     * Two plain lists side by side become the "applies / does not apply"
     * block; a single one becomes a bulleted paragraph.
     *
     * @param  array<int, ElementorNode>  $widgets
     * @return array{0: array<string, mixed>|null, 1: int}
     */
    private function mapLists(array $widgets, int $index): array
    {
        $length = $this->runLength($widgets, $index, fn (ElementorNode $node): bool => $node->is('icon-list'));
        $lists = [];

        for ($i = $index; $i < $index + $length; $i++) {
            $items = [];

            foreach ($widgets[$i]->rows('icon_list') as $item) {
                $text = $this->clean((string) ($item['text'] ?? ''));

                if ($text === '') {
                    continue;
                }

                if ($this->claims->rejects($text)) {
                    $this->note('Sağlık reklam kurallarına aykırı madde çıkarıldı: "'.$text.'"');

                    continue;
                }

                $items[] = $text;
            }

            if ($items !== []) {
                $lists[] = $items;
            }
        }

        $title = $this->titleBefore($widgets, $index);

        if (count($lists) >= 2) {
            return [$this->factory->twoColumnLists($title, null, $lists[0], null, $lists[1]), $length];
        }

        if ($lists === []) {
            return [null, $length];
        }

        $body = '<ul>'.implode('', array_map(fn (string $item): string => '<li>'.e($item).'</li>', $lists[0])).'</ul>';

        return [$this->factory->richText($title, $body), $length];
    }

    /**
     * A heading introduces whatever comes after it. On its own it is a section
     * label with nothing under it and is dropped.
     *
     * @param  array<int, ElementorNode>  $widgets
     * @return array{0: array<string, mixed>|null, 1: int}|null
     */
    private function mapHeading(array $widgets, int $index, bool $atStart): ?array
    {
        $title = $this->clean($widgets[$index]->html());
        $next = $widgets[$index + 1] ?? null;
        $sameSection = $next !== null
            && ($this->sections[$index] ?? null) === ($this->sections[$index + 1] ?? null);

        if ($next === null) {
            if ($title !== '') {
                $this->note('Altında içerik kalmayan başlık atlandı: "'.mb_substr($title, 0, 60, 'UTF-8').'"');
            }

            return [null, 1];
        }

        if ($next->is('text-editor') && $sameSection) {
            $mapped = $this->mapProse($widgets, $index + 1, $title, $atStart);

            return [$mapped[0], $mapped[1] + 1];
        }

        /* The heading belongs to the block that follows; that block picks it
           up through titleBefore(). */
        return [null, 1];
    }

    /**
     * A run of text editors is one body of prose.
     *
     * @param  array<int, ElementorNode>  $widgets
     * @return array{0: array<string, mixed>|null, 1: int}
     */
    private function mapProse(array $widgets, int $index, ?string $title, bool $atStart): array
    {
        $length = $this->runLength($widgets, $index, fn (ElementorNode $node): bool => $node->is('text-editor'));
        $parts = [];

        for ($i = $index; $i < $index + $length; $i++) {
            if ($body = $this->body((string) $widgets[$i]->stringSetting('editor'))) {
                $parts[] = $body;
            }
        }

        $body = implode('', $parts);

        if ($body === '') {
            return [null, $length];
        }

        $title ??= $this->titleBefore($widgets, $index);

        /* The first thing on a page is its opening statement, not body copy. */
        if ($atStart && $title !== null && $this->wordCount($body) < 90) {
            return [$this->factory->hero($title, $this->sanitizer->text($body)), $length];
        }

        return [$this->factory->richText($title, $body, showToc: $this->wordCount($body) > 400), $length];
    }

    /**
     * Hand-written HTML: a table becomes a comparison, anything else with real
     * words becomes prose, and the rest was styling or structured data.
     *
     * @return array{0: array<string, mixed>|null, 1: int}
     */
    private function mapHtml(ElementorNode $widget): array
    {
        $html = (string) $widget->stringSetting('html');

        if (preg_match('#<table\b#i', $html) === 1 && ($table = $this->comparisonFromTable($html))) {
            return [$table, 1];
        }

        $body = $this->body($html);

        if ($this->wordCount($body) < 20) {
            $this->note('Gömülü HTML bloğu atlandı (metin içermiyor).');

            return [null, 1];
        }

        return [$this->factory->richText(null, $body, showToc: $this->wordCount($body) > 400), 1];
    }

    /**
     * @return array{type: string, data: array<string, mixed>}|null
     */
    private function comparisonFromTable(string $html): ?array
    {
        $rows = [];
        preg_match_all('#<tr\b[^>]*>(.*?)</tr>#is', $html, $matches);

        foreach ($matches[1] ?? [] as $row) {
            preg_match_all('#<t[hd]\b[^>]*>(.*?)</t[hd]>#is', $row, $cells);
            $values = array_map(fn (string $cell): string => $this->clean($cell), $cells[1] ?? []);

            if (count($values) >= 3) {
                $rows[] = array_slice($values, 0, 3);
            }
        }

        if (count($rows) < 2) {
            return null;
        }

        $header = array_shift($rows);

        return $this->factory->comparisonTable(
            null,
            $header[1],
            $header[2],
            array_map(fn (array $row): array => ['label' => $row[0], 'value_a' => $row[1], 'value_b' => $row[2]], $rows),
        );
    }

    /**
     * A bare image carries no text; it is kept as a media reference so the
     * media step can attach it, but it becomes no block of its own.
     *
     * @return array{0: null, 1: int}
     */
    private function mapImage(ElementorNode $widget): array
    {
        $this->rememberImage($widget->imageId());

        return [null, 1];
    }

    /**
     * The heading immediately before a run.
     *
     * It only counts when it sits in the same top-level container: a heading
     * one section up introduced something that has since been dropped, and
     * would otherwise be attached to whatever happens to follow it.
     *
     * @param  array<int, ElementorNode>  $widgets
     */
    private function titleBefore(array $widgets, int $index): ?string
    {
        $previous = $widgets[$index - 1] ?? null;

        if ($previous === null || ! $previous->is('heading')) {
            return null;
        }

        if (($this->sections[$index - 1] ?? null) !== ($this->sections[$index] ?? null)) {
            return null;
        }

        $title = $this->clean($previous->html());

        /* A paragraph of small print is not a section title. */
        return $title === '' || mb_strlen($title, 'UTF-8') > 90 ? null : $title;
    }

    /**
     * How many widgets from `$index` belong to the same run.
     *
     * `$skippable` lets a run survive filler between its members — an icon
     * between two counters, say — without swallowing whatever follows it: the
     * run ends at the last widget that actually matched.
     *
     * @param  array<int, ElementorNode>  $widgets
     * @param  callable(ElementorNode): bool  $matches
     * @param  (callable(ElementorNode): bool)|null  $skippable
     */
    private function runLength(array $widgets, int $index, callable $matches, ?callable $skippable = null): int
    {
        $length = 0;
        $offset = 0;

        while (isset($widgets[$index + $offset])) {
            $widget = $widgets[$index + $offset];

            if ($matches($widget)) {
                $offset++;
                $length = $offset;

                continue;
            }

            if ($skippable !== null && $skippable($widget)) {
                $offset++;

                continue;
            }

            break;
        }

        return max(1, $length);
    }

    /**
     * Sanitized body HTML with the promotional claims taken out.
     */
    private function body(string $html): string
    {
        $before = $this->claims->removed();
        $scrubbed = $this->claims->scrub($html);

        foreach (array_diff($this->claims->removed(), $before) as $claim) {
            $this->note('Sağlık reklam kurallarına aykırı ifade çıkarıldı: "'.$claim.'"');
        }

        return $this->titles->fixTypos($this->sanitizer->clean($scrubbed));
    }

    /**
     * Plain, corrected text of a fragment.
     *
     * Tags become spaces rather than nothing: Elementor headings wrap half the
     * line in a `span`, and stripping the tag outright would run the two
     * halves together.
     */
    private function clean(string $html): string
    {
        $text = preg_replace('/<[^>]*>/', ' ', $html) ?? $html;

        return $this->titles->fixTypos(TurkishText::normalize($text));
    }

    private function innerHtml(ElementorNode $container): string
    {
        $parts = [];

        foreach ($container->widgets() as $widget) {
            if ($widget->is('text-editor', 'heading', 'html')) {
                $parts[] = $widget->html();
            }
        }

        return implode(' ', $parts);
    }

    private function wordCount(string $html): int
    {
        $text = $this->sanitizer->text($html);

        return $text === '' ? 0 : count(preg_split('/\s+/u', $text) ?: []);
    }

    /**
     * Keeps a block only when the site can render it, so nothing is written
     * that would silently disappear from the page.
     *
     * @param  array{type: string, data: array<string, mixed>}  $block
     * @return array{type: string, data: array<string, mixed>}
     */
    private function requireBlock(array $block): array
    {
        if (! BlockFactory::supports($block['type'])) {
            $this->note("`{$block['type']}` bloğu henüz tanımlı değil; içerik kaydedildi ama blok eklenene kadar görünmeyecek.");
        }

        return $block;
    }

    private function titleKey(string $title): string
    {
        return TurkishText::slug($this->titles->fixTypos(TurkishText::normalize(strip_tags($title))));
    }

    /**
     * The same words in a fixed order, so `Bel Ağrıları ve Bacak Ağrıları` on
     * a card still finds the `Bel ve Bacak Ağrıları` record.
     */
    private function tokenKey(string $title): string
    {
        $tokens = array_values(array_unique(array_filter(explode('-', $this->titleKey($title)))));
        sort($tokens);

        return count($tokens) < 2 ? '' : implode('-', $tokens);
    }

    private function rememberImage(mixed $id): void
    {
        if (is_numeric($id) && (int) $id > 0) {
            $this->imageIds[] = (int) $id;
        }
    }

    private function noteSkipped(ElementorNode $widget, string $reason): void
    {
        $label = $widget->widgetType ?? $widget->elementType;
        $text = $widget->text();
        $excerpt = $text === '' ? '' : ' — "'.mb_substr($text, 0, 60, 'UTF-8').'"';

        $this->note("Atlandı: {$label} ({$reason}){$excerpt}");
    }

    private function note(string $note): void
    {
        if (! in_array($note, $this->notes, true)) {
            $this->notes[] = $note;
        }
    }
}
