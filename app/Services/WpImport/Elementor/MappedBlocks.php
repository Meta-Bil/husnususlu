<?php

namespace App\Services\WpImport\Elementor;

/**
 * What came out of one Elementor document: the blocks, plus a record of
 * everything that was left behind so an editor can check the page.
 */
class MappedBlocks
{
    /**
     * @param  array<int, array{type: string, data: array<string, mixed>}>  $blocks
     * @param  array<int, string>  $notes
     * @param  array<int, int>  $imageIds  WordPress attachment ids used by the page.
     */
    public function __construct(
        public readonly array $blocks = [],
        public readonly array $notes = [],
        public readonly array $imageIds = [],
    ) {}

    public function isEmpty(): bool
    {
        return $this->blocks === [];
    }

    /**
     * @return array<int, string>
     */
    public function types(): array
    {
        return array_map(fn (array $block): string => $block['type'], $this->blocks);
    }

    public function withBlocks(array $blocks): self
    {
        return new self($blocks, $this->notes, $this->imageIds);
    }
}
