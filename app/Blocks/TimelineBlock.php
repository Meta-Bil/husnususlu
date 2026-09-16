<?php

namespace App\Blocks;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

/**
 * Education and career, one dated row at a time, in one or two columns.
 */
class TimelineBlock extends AbstractBlock
{
    public static function key(): string
    {
        return 'timeline';
    }

    public static function label(): string
    {
        return 'Eğitim ve kariyer zaman çizelgesi';
    }

    public static function icon(): string
    {
        return 'heroicon-o-academic-cap';
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

            Repeater::make('groups')
                ->label('Sütunlar')
                ->schema([
                    LocaleTabs::make(fn (string $locale): array => [
                        TextInput::make("label.{$locale}")
                            ->label('Sütun başlığı')
                            ->maxLength(60),
                    ]),
                    Repeater::make('items')
                        ->label('Satırlar')
                        ->schema([
                            TextInput::make('year')
                                ->label('Yıl')
                                ->required()
                                ->maxLength(40),
                            LocaleTabs::make(fn (string $locale): array => [
                                TextInput::make("title.{$locale}")->label('Kurum')->maxLength(160),
                                Textarea::make("institution.{$locale}")->label('Açıklama')->rows(2),
                            ]),
                        ])
                        ->defaultItems(0)
                        ->collapsed()
                        ->itemLabel(fn (array $state): ?string => $state['year'] ?? null),
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
        $groups = [];

        foreach ($data['groups'] ?? [] as $group) {
            $groups[] = [
                'label' => static::text($group, 'label', $locale),
                'items' => static::rows($group['items'] ?? [], ['title', 'institution'], $locale),
            ];
        }

        return [
            'eyebrow' => static::text($data, 'eyebrow', $locale),
            'title' => static::text($data, 'title', $locale),
            'accent' => static::text($data, 'accent', $locale),
            'lead' => static::text($data, 'lead', $locale),
            'groups' => $groups,
        ];
    }
}
