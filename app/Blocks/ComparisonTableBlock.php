<?php

namespace App\Blocks;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

/**
 * Two methods side by side: a heading column on the left and one row per
 * criterion, with the recommended option highlighted.
 */
class ComparisonTableBlock extends AbstractBlock
{
    public static function key(): string
    {
        return 'comparison_table';
    }

    public static function label(): string
    {
        return 'Karşılaştırma tablosu';
    }

    public static function icon(): string
    {
        return 'heroicon-o-table-cells';
    }

    public static function schema(): array
    {
        return [
            LocaleTabs::make(fn (string $locale): array => [
                TextInput::make("eyebrow.{$locale}")->label('Üst etiket')->maxLength(80),
                Textarea::make("title.{$locale}")->label('Başlık')->rows(2),
                TextInput::make("accent.{$locale}")->label('Vurgulu kelime (altın renkli)')->maxLength(80),
                Textarea::make("lead.{$locale}")->label('Giriş metni')->rows(3),
            ]),

            Section::make('Sütun başlıkları')
                ->schema([
                    LocaleTabs::make(fn (string $locale): array => [
                        TextInput::make("column_a.{$locale}")
                            ->label('Birinci sütun (öne çıkan)')
                            ->required($locale === config('locales.default'))
                            ->maxLength(60),
                        TextInput::make("column_b.{$locale}")
                            ->label('İkinci sütun')
                            ->required($locale === config('locales.default'))
                            ->maxLength(60),
                    ]),
                ])
                ->columns(1),

            Repeater::make('rows')
                ->label('Satırlar')
                ->schema([
                    LocaleTabs::make(fn (string $locale): array => [
                        TextInput::make("label.{$locale}")->label('Ölçüt')->maxLength(80),
                        TextInput::make("value_a.{$locale}")->label('Birinci sütun')->maxLength(120),
                        TextInput::make("value_b.{$locale}")->label('İkinci sütun')->maxLength(120),
                    ]),
                ])
                ->defaultItems(0)
                ->collapsed()
                ->itemLabel(fn (array $state): ?string => $state['label'][config('locales.default')] ?? null),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function transform(array $data, string $locale): array
    {
        return [
            'eyebrow' => static::text($data, 'eyebrow', $locale),
            'title' => static::text($data, 'title', $locale),
            'accent' => static::text($data, 'accent', $locale),
            'lead' => static::text($data, 'lead', $locale),
            'columnA' => static::text($data, 'column_a', $locale),
            'columnB' => static::text($data, 'column_b', $locale),
            'rows' => static::rows($data['rows'] ?? [], ['label', 'value_a', 'value_b'], $locale),
        ];
    }
}
