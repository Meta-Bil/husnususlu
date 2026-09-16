<?php

namespace App\Blocks;

use App\Blocks\Concerns\InteractsWithContent;
use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Storage;

/**
 * The treatment path, numbered with roman numerals, optionally introduced by a
 * wide photograph.
 */
class ProcessStepsBlock extends AbstractBlock
{
    use InteractsWithContent;

    public static function key(): string
    {
        return 'process_steps';
    }

    public static function label(): string
    {
        return 'Süreç adımları';
    }

    public static function icon(): string
    {
        return 'heroicon-o-arrow-long-right';
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

            FileUpload::make('image')
                ->label('Fotoğraf (adımların üstünde)')
                ->image()
                ->disk('public')
                ->directory('blocks/process-steps')
                ->imageEditor(),

            Repeater::make('steps')
                ->label('Adımlar')
                ->schema([
                    LocaleTabs::make(fn (string $locale): array => [
                        TextInput::make("title.{$locale}")->label('Adım başlığı')->maxLength(80),
                        Textarea::make("text.{$locale}")->label('Açıklama')->rows(3),
                    ]),
                ])
                ->defaultItems(0)
                ->collapsed()
                ->itemLabel(fn (array $state): ?string => $state['title'][config('locales.default')] ?? null),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function transform(array $data, string $locale): array
    {
        $steps = static::rows($data['steps'] ?? [], ['title', 'text'], $locale);

        foreach ($steps as $index => $step) {
            $steps[$index]['number'] = static::roman($index + 1);
        }

        return [
            'eyebrow' => static::text($data, 'eyebrow', $locale),
            'title' => static::text($data, 'title', $locale),
            'accent' => static::text($data, 'accent', $locale),
            'lead' => static::text($data, 'lead', $locale),
            'image' => filled($data['image'] ?? null) ? Storage::disk('public')->url($data['image']) : null,
            'steps' => $steps,
        ];
    }
}
