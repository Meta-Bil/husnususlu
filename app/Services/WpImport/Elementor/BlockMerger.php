<?php

namespace App\Services\WpImport\Elementor;

/**
 * Welds the blocks of a page's translations into one record.
 *
 * Each language is mapped on its own, which gives two lists of blocks built
 * from the same Elementor template. Pairing them by type in reading order puts
 * the English text beside the Turkish text inside a single block, which is how
 * the page builder stores translations.
 */
class BlockMerger
{
    /**
     * @param  array<int, array{type: string, data: array<string, mixed>}>  $primary
     * @param  array<int, array{type: string, data: array<string, mixed>}>  $secondary
     * @return array{blocks: array<int, array{type: string, data: array<string, mixed>}>, unmatched: int}
     */
    public static function merge(array $primary, array $secondary): array
    {
        $used = [];
        $blocks = [];

        foreach ($primary as $block) {
            $match = self::nextOfType($secondary, $block['type'], $used);

            if ($match !== null) {
                $used[$match] = true;
                $block['data'] = self::mergeData($block['data'], $secondary[$match]['data']);
            }

            $blocks[] = $block;
        }

        return ['blocks' => $blocks, 'unmatched' => count($secondary) - count($used)];
    }

    /**
     * @param  array<int, array{type: string, data: array<string, mixed>}>  $blocks
     * @param  array<int, bool>  $used
     */
    private static function nextOfType(array $blocks, string $type, array $used): ?int
    {
        foreach ($blocks as $index => $block) {
            if (! isset($used[$index]) && $block['type'] === $type) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Merges two block payloads, position by position, keeping the primary
     * language's structure and only adding translations to its leaves.
     *
     * @param  array<string, mixed>  $primary
     * @param  array<string, mixed>  $secondary
     * @return array<string, mixed>
     */
    private static function mergeData(array $primary, array $secondary): array
    {
        foreach ($secondary as $key => $value) {
            if (! array_key_exists($key, $primary)) {
                $primary[$key] = $value;

                continue;
            }

            if (! is_array($value) || ! is_array($primary[$key])) {
                continue;
            }

            $primary[$key] = self::isList($value) && self::isList($primary[$key])
                ? self::mergeLists($primary[$key], $value)
                : self::mergeData($primary[$key], $value);
        }

        return $primary;
    }

    /**
     * @param  array<int, mixed>  $primary
     * @param  array<int, mixed>  $secondary
     * @return array<int, mixed>
     */
    private static function mergeLists(array $primary, array $secondary): array
    {
        foreach ($primary as $index => $row) {
            if (is_array($row) && is_array($secondary[$index] ?? null)) {
                $primary[$index] = self::mergeData($row, $secondary[$index]);
            }
        }

        return $primary;
    }

    /**
     * @param  array<mixed>  $value
     */
    private static function isList(array $value): bool
    {
        return $value === [] || array_is_list($value);
    }
}
