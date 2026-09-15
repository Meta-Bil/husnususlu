<?php

namespace App\Blocks\Contracts;

/**
 * A content block: one editable section of a page.
 *
 * Every block owns its admin form schema, its Blade view and, where relevant,
 * the structured data it contributes to the page's JSON-LD.
 */
interface Block
{
    /**
     * Stable key stored in the `blocks` JSON column and used as the view name.
     */
    public static function key(): string;

    public static function label(): string;

    public static function icon(): string;

    /**
     * @return array<int, \Filament\Schemas\Components\Component|\Filament\Forms\Components\Field>
     */
    public static function schema(): array;

    public static function view(): string;

    /**
     * Turns stored data into what the view needs: translated strings, resolved
     * relations and media URLs.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function transform(array $data, string $locale): array;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>|null
     */
    public static function jsonLd(array $data, string $locale): ?array;
}
