<?php

namespace App\Blocks;

use App\Blocks\Contracts\Block;
use Filament\Forms\Components\Builder\Block as BuilderBlock;
use Illuminate\Support\HtmlString;

/**
 * The single source of truth for page blocks: what the editor can add, how it
 * is rendered and what structured data it contributes.
 */
class BlockRegistry
{
    /**
     * @var array<int, class-string<Block>>
     */
    public const BLOCKS = [
        HeroBlock::class,
        StatsBlock::class,
        MediaStripBlock::class,
        TreatmentHighlightBlock::class,
        TreatmentIndexBlock::class,
        ComparisonTableBlock::class,
        ProcessStepsBlock::class,
        RichTextBlock::class,
        ImageTextBlock::class,
        TwoColumnListsBlock::class,
        FaqBlock::class,
        VideoGridBlock::class,
        LatestPostsBlock::class,
        DoctorProfileBlock::class,
        TimelineBlock::class,
        PackagesBlock::class,
        RelatedTreatmentsBlock::class,
        PublicationsLinkBlock::class,
        ContactDetailsBlock::class,
        AppointmentBlock::class,
        CtaBandBlock::class,
    ];

    /**
     * @return array<string, class-string<Block>>
     */
    public static function map(): array
    {
        $map = [];

        foreach (self::BLOCKS as $block) {
            $map[$block::key()] = $block;
        }

        return $map;
    }

    /**
     * @return class-string<Block>|null
     */
    public static function find(string $key): ?string
    {
        return self::map()[$key] ?? null;
    }

    /**
     * Blocks for the admin's page builder.
     *
     * @return array<int, BuilderBlock>
     */
    public static function builderBlocks(): array
    {
        return array_map(
            fn (string $block): BuilderBlock => BuilderBlock::make($block::key())
                ->label($block::label())
                ->icon($block::icon())
                ->schema($block::schema()),
            self::BLOCKS,
        );
    }

    /**
     * @param  array<int, array{type: string, data: array<string, mixed>}>|null  $blocks
     */
    public static function render(?array $blocks, ?string $locale = null): HtmlString
    {
        $locale ??= app()->getLocale();
        $html = '';

        foreach ($blocks ?? [] as $block) {
            $class = self::find($block['type'] ?? '');

            if (! $class) {
                continue;
            }

            $html .= view($class::view(), $class::transform($block['data'] ?? [], $locale))->render();
        }

        return new HtmlString($html);
    }

    /**
     * Structured data contributed by the blocks of a page.
     *
     * @param  array<int, array{type: string, data: array<string, mixed>}>|null  $blocks
     * @return array<int, array<string, mixed>>
     */
    public static function jsonLd(?array $blocks, ?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $graph = [];

        foreach ($blocks ?? [] as $block) {
            $class = self::find($block['type'] ?? '');

            if (! $class) {
                continue;
            }

            if ($data = $class::jsonLd($block['data'] ?? [], $locale)) {
                $graph[] = $data;
            }
        }

        return $graph;
    }
}
