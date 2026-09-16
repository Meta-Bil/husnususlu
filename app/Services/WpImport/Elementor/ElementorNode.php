<?php

namespace App\Services\WpImport\Elementor;

use App\Services\WpImport\Support\TurkishText;

/**
 * One node of an Elementor document: a container or a widget.
 *
 * Elementor keeps everything in a single JSON blob of nested `elements`, where
 * the meaningful part of a widget is a handful of keys inside `settings` whose
 * names differ per widget type. This wraps that away.
 */
class ElementorNode
{
    /** Where each widget type keeps its visible text, in reading order. */
    private const TEXT_KEYS = [
        'heading' => ['title'],
        'text-editor' => ['editor'],
        'html' => ['html'],
        'icon-box' => ['title_text', 'description_text'],
        'image-box' => ['title_text', 'description_text'],
        'button' => ['text'],
        'counter' => ['prefix', 'ending_number', 'suffix', 'title'],
        'call-to-action' => ['title', 'description'],
        'price-table' => ['heading', 'sub_heading', 'period'],
        'divider' => [],
        'animated-headline' => ['headline_text', 'before_text', 'after_text'],
        'theme-post-title' => ['title'],
    ];

    /**
     * @param  array<string, mixed>  $settings
     * @param  array<int, self>  $children
     */
    public function __construct(
        public readonly string $elementType,
        public readonly ?string $widgetType,
        public readonly array $settings,
        public readonly array $children,
    ) {}

    /**
     * @param  array<string, mixed>  $raw
     */
    public static function fromArray(array $raw): self
    {
        $children = [];

        foreach ($raw['elements'] ?? [] as $child) {
            if (is_array($child)) {
                $children[] = self::fromArray($child);
            }
        }

        return new self(
            (string) ($raw['elType'] ?? 'container'),
            isset($raw['widgetType']) ? (string) $raw['widgetType'] : null,
            is_array($raw['settings'] ?? null) ? $raw['settings'] : [],
            $children,
        );
    }

    public function isWidget(): bool
    {
        return $this->elementType === 'widget' && $this->widgetType !== null;
    }

    public function is(string ...$widgetTypes): bool
    {
        return $this->widgetType !== null && in_array($this->widgetType, $widgetTypes, true);
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    public function stringSetting(string $key): ?string
    {
        $value = $this->settings[$key] ?? null;

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return is_string($value) && trim($value) !== '' ? $value : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function rows(string $key): array
    {
        $rows = $this->settings[$key] ?? [];

        if (! is_array($rows)) {
            return [];
        }

        return array_values(array_filter($rows, is_array(...)));
    }

    /**
     * The widget's own HTML, before any sanitizing.
     */
    public function html(): string
    {
        $parts = [];

        foreach (self::TEXT_KEYS[$this->widgetType] ?? [] as $key) {
            if ($value = $this->stringSetting($key)) {
                $parts[] = $value;
            }
        }

        return implode(' ', $parts);
    }

    /**
     * The widget's visible text, normalised for comparison and fingerprints.
     */
    public function text(): string
    {
        $html = preg_replace('#<\s*(script|style)\b[^>]*>.*?<\s*/\s*\1\s*>#is', '', $this->html()) ?? '';

        return TurkishText::normalize(strip_tags($html));
    }

    /**
     * A stable identity for the widget's text, used to spot furniture that
     * Elementor repeated on every page.
     */
    public function fingerprint(): ?string
    {
        $text = TurkishText::lower($this->text());

        if (mb_strlen($text, 'UTF-8') < 12) {
            return null;
        }

        return sha1(mb_substr($text, 0, 200, 'UTF-8'));
    }

    /**
     * The WordPress attachment id of the widget's image, if it has one.
     */
    public function imageId(): ?int
    {
        foreach (['image', 'bg_image', 'image_overlay', 'background_image', 'graphic_image', 'selected_icon'] as $key) {
            $value = $this->settings[$key] ?? null;

            if (! is_array($value)) {
                continue;
            }

            $candidate = $value['value'] ?? $value;
            $id = is_array($candidate) ? ($candidate['id'] ?? null) : null;

            if (is_numeric($id) && (int) $id > 0) {
                return (int) $id;
            }
        }

        return null;
    }

    /**
     * Every link this widget points at, used to tell a contact rail from a
     * list of real content links.
     *
     * @return array<int, string>
     */
    public function links(): array
    {
        $links = [];

        array_walk_recursive($this->settings, function (mixed $value, int|string $key) use (&$links): void {
            if ($key === 'url' && is_string($value) && $value !== '') {
                $links[] = $value;
            }
        });

        return $links;
    }

    /**
     * This node and all of its descendants, depth first.
     *
     * @return array<int, self>
     */
    public function flatten(): array
    {
        $nodes = [$this];

        foreach ($this->children as $child) {
            $nodes = array_merge($nodes, $child->flatten());
        }

        return $nodes;
    }

    /**
     * Every widget of the subtree, in reading order.
     *
     * @return array<int, self>
     */
    public function widgets(): array
    {
        return array_values(array_filter($this->flatten(), fn (self $node): bool => $node->isWidget()));
    }
}
