<?php

namespace App\Services\WpImport\Elementor;

use App\Blocks\BlockRegistry;
use Illuminate\Support\Str;

/**
 * Builds the arrays the page builder stores.
 *
 * A block is `['type' => key, 'data' => [...]]` and every translatable leaf
 * inside `data` is a map of locale => value, so one record holds every
 * language. The importer fills in a single locale at a time and
 * {@see BlockMerger} welds the languages together afterwards.
 */
class BlockFactory
{
    public function __construct(private readonly string $locale) {}

    public function locale(): string
    {
        return $this->locale;
    }

    /**
     * Whether the site has a block class for this key yet. Blocks are still
     * being added, so the importer records what it could not place instead of
     * writing data no view can render.
     */
    public static function supports(string $key): bool
    {
        if (BlockRegistry::find($key) !== null) {
            return true;
        }

        return class_exists('App\\Blocks\\'.Str::studly($key).'Block');
    }

    /**
     * A translatable leaf.
     *
     * @return array<string, string>
     */
    public function t(?string $value): array
    {
        return filled($value) ? [$this->locale => $value] : [];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{type: string, data: array<string, mixed>}
     */
    public function block(string $type, array $data): array
    {
        return ['type' => $type, 'data' => $data];
    }

    /**
     * @param  array<int, array{value: string|null, label: string|null}>  $facts
     * @return array{type: string, data: array<string, mixed>}
     */
    public function hero(
        ?string $title,
        ?string $lead = null,
        ?string $eyebrow = null,
        ?string $image = null,
        array $facts = [],
        string $variant = 'page',
    ): array {
        return $this->block('hero', array_filter([
            'variant' => $facts !== [] && $image === null ? 'facts' : $variant,
            'eyebrow' => $this->t($eyebrow),
            'title' => $this->t($title),
            'lead' => $this->t($lead),
            'image' => $image,
            'show_cta' => true,
            'show_phone' => true,
            'facts' => array_map(fn (array $fact): array => [
                'value' => $this->t($fact['value'] ?? null),
                'label' => $this->t($fact['label'] ?? null),
            ], $facts),
        ], fn (mixed $value): bool => $value !== [] && $value !== null));
    }

    /**
     * @param  array<int, array{value: string, label: string|null}>  $items
     * @return array{type: string, data: array<string, mixed>}
     */
    public function stats(array $items): array
    {
        return $this->block('stats', [
            'use_settings' => false,
            'items' => array_map(fn (array $item): array => [
                'value' => $item['value'],
                'label' => $this->t($item['label'] ?? null),
            ], $items),
        ]);
    }

    /**
     * @return array{type: string, data: array<string, mixed>}
     */
    public function richText(?string $title, string $body, ?string $eyebrow = null, bool $showToc = false): array
    {
        return $this->block('rich_text', array_filter([
            'background' => 'paper',
            'show_toc' => $showToc,
            'eyebrow' => $this->t($eyebrow),
            'title' => $this->t($title),
            'body' => $this->t($body),
        ], fn (mixed $value): bool => $value !== []));
    }

    /**
     * @param  array<int, array{question: string, answer: string}>  $items
     * @return array{type: string, data: array<string, mixed>}
     */
    public function faq(?string $title, array $items): array
    {
        return $this->block('faq', array_filter([
            'title' => $this->t($title),
            'items' => array_map(fn (array $item): array => [
                'question' => $this->t($item['question']),
                'answer' => $this->t($item['answer']),
            ], $items),
        ], fn (mixed $value): bool => $value !== []));
    }

    /**
     * @param  array<int, array{title: string|null, text: string|null, url: string|null}>  $cards
     * @return array{type: string, data: array<string, mixed>}
     */
    public function treatmentHighlight(?string $title, ?string $lead, array $cards): array
    {
        return $this->block('treatment_highlight', array_filter([
            'title' => $this->t($title),
            'lead' => $this->t($lead),
            'card_source' => 'manual',
            'cards' => array_map(fn (array $card): array => array_filter([
                'title' => $this->t($card['title'] ?? null),
                'text' => $this->t($card['text'] ?? null),
                'url' => $card['url'] ?? null,
            ], fn (mixed $value): bool => $value !== [] && $value !== null), $cards),
        ], fn (mixed $value): bool => $value !== []));
    }

    /**
     * @param  array<int, int>  $painTypeIds
     * @param  array<int, int>  $procedureIds
     * @return array{type: string, data: array<string, mixed>}
     */
    public function treatmentIndex(?string $title, ?string $lead, array $painTypeIds, array $procedureIds = []): array
    {
        return $this->block('treatment_index', array_filter([
            'title' => $this->t($title),
            'lead' => $this->t($lead),
            'pain_type_ids' => array_values($painTypeIds),
            'show_procedures' => $procedureIds !== [],
            'procedure_ids' => array_values($procedureIds),
        ], fn (mixed $value): bool => $value !== []));
    }

    /**
     * @param  array<int, array{title: string|null, text: string|null}>  $steps
     * @return array{type: string, data: array<string, mixed>}
     */
    public function processSteps(?string $title, array $steps): array
    {
        return $this->block('process_steps', array_filter([
            'title' => $this->t($title),
            'steps' => array_map(fn (array $step): array => [
                'title' => $this->t($step['title'] ?? null),
                'text' => $this->t($step['text'] ?? null),
            ], $steps),
        ], fn (mixed $value): bool => $value !== []));
    }

    /**
     * A video block naming specific films.
     *
     * The films are picked by record id. When the import has no record for a
     * link — the video step has not run yet, or the film is gone from the
     * gallery — the block falls back to showing the newest videos instead of
     * rendering nothing.
     *
     * @param  array<int, int>  $videoIds
     * @return array{type: string, data: array<string, mixed>}
     */
    public function videoGrid(?string $title, array $videoIds = [], int $limit = 3): array
    {
        $videoIds = array_values(array_unique($videoIds));

        return $this->block('video_grid', array_filter([
            'title' => $this->t($title),
            'source' => $videoIds === [] ? 'category' : 'selected',
            'video_ids' => $videoIds,
            'limit' => $videoIds === [] ? $limit : max(1, count($videoIds)),
            'show_featured' => count($videoIds) === 1,
        ], fn (mixed $value): bool => $value !== [] && $value !== null));
    }

    /**
     * @param  array<int, string>  $positive
     * @param  array<int, string>  $negative
     * @return array{type: string, data: array<string, mixed>}
     */
    public function twoColumnLists(?string $title, ?string $positiveTitle, array $positive, ?string $negativeTitle, array $negative): array
    {
        return $this->block('two_column_lists', array_filter([
            'title' => $this->t($title),
            'positive_title' => $this->t($positiveTitle),
            'positive_items' => $this->t($positive === [] ? null : implode("\n", $positive)),
            'negative_title' => $this->t($negativeTitle),
            'negative_items' => $this->t($negative === [] ? null : implode("\n", $negative)),
        ], fn (mixed $value): bool => $value !== []));
    }

    /**
     * @param  array<int, array{label: string, value_a: string, value_b: string}>  $rows
     * @return array{type: string, data: array<string, mixed>}
     */
    public function comparisonTable(?string $title, string $columnA, string $columnB, array $rows): array
    {
        return $this->block('comparison_table', array_filter([
            'title' => $this->t($title),
            'column_a' => $this->t($columnA),
            'column_b' => $this->t($columnB),
            'rows' => array_map(fn (array $row): array => [
                'label' => $this->t($row['label']),
                'value_a' => $this->t($row['value_a']),
                'value_b' => $this->t($row['value_b']),
            ], $rows),
        ], fn (mixed $value): bool => $value !== []));
    }

    /**
     * @param  array<int, array{name: string, eyebrow: string|null, features: array<int, string>, button_label: string|null, button_url: string|null}>  $packages
     * @return array{type: string, data: array<string, mixed>}
     */
    public function packages(?string $title, array $packages): array
    {
        return $this->block('packages', array_filter([
            'title' => $this->t($title),
            'items' => array_map(fn (array $package): array => array_filter([
                'title' => $this->t($package['name']),
                'eyebrow' => $this->t($package['eyebrow'] ?? null),
                'features' => $this->t($package['features'] === [] ? null : implode("\n", $package['features'])),
                'button_label' => $this->t($package['button_label'] ?? null),
                'button_url' => $package['button_url'] ?? null,
            ], fn (mixed $value): bool => $value !== [] && $value !== null), $packages),
        ], fn (mixed $value): bool => $value !== []));
    }
}
