<?php

namespace App\Services\WpImport\Elementor;

/**
 * An Elementor page as a tree of nodes, plus the flat reading order the block
 * mapper works from.
 */
class ElementorDocument
{
    /**
     * @param  array<int, ElementorNode>  $roots
     */
    public function __construct(
        public readonly int $wpId,
        public readonly array $roots,
    ) {}

    /**
     * @param  array<int, array<string, mixed>>  $data  The decoded `_elementor_data`.
     */
    public static function fromArray(int $wpId, array $data): self
    {
        $roots = [];

        foreach ($data as $node) {
            if (is_array($node)) {
                $roots[] = ElementorNode::fromArray($node);
            }
        }

        return new self($wpId, $roots);
    }

    public static function fromJson(int $wpId, string $json): self
    {
        $decoded = json_decode($json, true);

        return self::fromArray($wpId, is_array($decoded) ? $decoded : []);
    }

    public function isEmpty(): bool
    {
        return $this->roots === [];
    }

    /**
     * Every widget on the page, in reading order.
     *
     * @return array<int, ElementorNode>
     */
    public function widgets(): array
    {
        $widgets = [];

        foreach ($this->roots as $root) {
            $widgets = array_merge($widgets, $root->widgets());
        }

        return $widgets;
    }

    /**
     * Widgets grouped by the container that holds them, so the mapper can see
     * which heading belongs to which text.
     *
     * @return array<int, array<int, ElementorNode>>
     */
    public function sections(): array
    {
        return array_values(array_filter(
            array_map(fn (ElementorNode $root): array => $root->widgets(), $this->roots),
            fn (array $widgets): bool => $widgets !== [],
        ));
    }
}
