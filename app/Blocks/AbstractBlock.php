<?php

namespace App\Blocks;

use App\Blocks\Contracts\Block;
use App\Support\Localization\Locales;

abstract class AbstractBlock implements Block
{
    public static function icon(): string
    {
        return 'heroicon-o-square-3-stack-3d';
    }

    public static function view(): string
    {
        return 'blocks.'.str_replace('_', '-', static::key());
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function transform(array $data, string $locale): array
    {
        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>|null
     */
    public static function jsonLd(array $data, string $locale): ?array
    {
        return null;
    }

    /**
     * Reads a translatable leaf of block data, falling back the same way model
     * fields do.
     *
     * @param  array<string, mixed>  $data
     */
    protected static function text(array $data, string $field, string $locale): ?string
    {
        $value = $data[$field] ?? null;

        if (is_string($value)) {
            return filled($value) ? $value : null;
        }

        if (! is_array($value)) {
            return null;
        }

        foreach (Locales::fallbackChain($locale) as $candidate) {
            if (filled($value[$candidate] ?? null)) {
                return $value[$candidate];
            }
        }

        foreach ($value as $candidate) {
            if (filled($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Same as text(), for a list of repeater rows.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<int, string>  $fields
     * @return array<int, array<string, mixed>>
     */
    protected static function rows(array $rows, array $fields, string $locale): array
    {
        return array_values(array_map(function (array $row) use ($fields, $locale): array {
            foreach ($fields as $field) {
                $row[$field] = static::text($row, $field, $locale);
            }

            return $row;
        }, $rows));
    }
}
