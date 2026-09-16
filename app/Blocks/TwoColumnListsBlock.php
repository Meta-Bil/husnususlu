<?php

namespace App\Blocks;

use App\Blocks\Concerns\InteractsWithContent;
use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

/**
 * Two contrasting cards — who a treatment is suitable for and who it is not —
 * with a tick list on the light side and a cross list on the dark one.
 */
class TwoColumnListsBlock extends AbstractBlock
{
    use InteractsWithContent;

    public static function key(): string
    {
        return 'two_column_lists';
    }

    public static function label(): string
    {
        return 'Karşıt listeler (uygulanır / uygulanmaz)';
    }

    public static function icon(): string
    {
        return 'heroicon-o-check-circle';
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

            Section::make('Uygulanır')
                ->schema([
                    LocaleTabs::make(fn (string $locale): array => [
                        TextInput::make("positive_label.{$locale}")->label('Üst etiket')->maxLength(60),
                        TextInput::make("positive_title.{$locale}")->label('Başlık')->maxLength(120),
                        Textarea::make("positive_items.{$locale}")
                            ->label('Maddeler')
                            ->helperText('Her satır bir madde.')
                            ->rows(5),
                        Textarea::make("positive_note.{$locale}")->label('Alt not')->rows(2),
                    ]),
                ])
                ->columns(1),

            Section::make('Uygulanmaz')
                ->schema([
                    LocaleTabs::make(fn (string $locale): array => [
                        TextInput::make("negative_label.{$locale}")->label('Üst etiket')->maxLength(60),
                        TextInput::make("negative_title.{$locale}")->label('Başlık')->maxLength(120),
                        Textarea::make("negative_items.{$locale}")
                            ->label('Maddeler')
                            ->helperText('Her satır bir madde.')
                            ->rows(5),
                        Textarea::make("negative_note.{$locale}")->label('Alt not')->rows(2),
                    ]),
                ])
                ->columns(1),
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
            'positive' => [
                'label' => static::text($data, 'positive_label', $locale),
                'title' => static::text($data, 'positive_title', $locale),
                'items' => static::lines(static::text($data, 'positive_items', $locale)),
                'note' => static::text($data, 'positive_note', $locale),
            ],
            'negative' => [
                'label' => static::text($data, 'negative_label', $locale),
                'title' => static::text($data, 'negative_title', $locale),
                'items' => static::lines(static::text($data, 'negative_items', $locale)),
                'note' => static::text($data, 'negative_note', $locale),
            ],
        ];
    }
}
