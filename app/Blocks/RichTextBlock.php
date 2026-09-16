<?php

namespace App\Blocks;

use App\Blocks\Concerns\InteractsWithContent;
use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Str;

/**
 * A body of editorial text. The second-level headings can be lifted into a
 * sticky table of contents next to the text, so long pages stay navigable.
 */
class RichTextBlock extends AbstractBlock
{
    use InteractsWithContent;

    public static function key(): string
    {
        return 'rich_text';
    }

    public static function label(): string
    {
        return 'Metin bölümü';
    }

    public static function icon(): string
    {
        return 'heroicon-o-document-text';
    }

    public static function schema(): array
    {
        return [
            Select::make('background')
                ->label('Zemin')
                ->options([
                    'paper' => 'Açık (kağıt)',
                    'ivory' => 'Açık (fildişi)',
                    'navy' => 'Koyu (lacivert)',
                ])
                ->default('paper')
                ->required(),

            Toggle::make('show_toc')
                ->label('İçindekiler sütunu')
                ->helperText('Metindeki ikinci düzey başlıklardan otomatik oluşturulur.')
                ->default(false)
                ->live(),

            LocaleTabs::make(fn (string $locale): array => [
                TextInput::make("eyebrow.{$locale}")->label('Üst etiket')->maxLength(80),
                Textarea::make("title.{$locale}")->label('Başlık')->rows(2),
                TextInput::make("accent.{$locale}")->label('Vurgulu kelime (altın renkli)')->maxLength(80),
                RichEditor::make("body.{$locale}")
                    ->label('Metin')
                    ->required($locale === config('locales.default')),
                TextInput::make("toc_label.{$locale}")
                    ->label('İçindekiler başlığı')
                    ->placeholder(__('front.contents', [], $locale))
                    ->maxLength(60),
                Textarea::make("aside.{$locale}")
                    ->label('İçindekiler altındaki not')
                    ->rows(3),
            ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function transform(array $data, string $locale): array
    {
        $body = static::sanitizeHtml(static::text($data, 'body', $locale));
        $headings = [];

        if ($data['show_toc'] ?? false) {
            $body = static::withHeadingIds($body, $headings);
        }

        return [
            'background' => $data['background'] ?? 'paper',
            'eyebrow' => static::text($data, 'eyebrow', $locale),
            'title' => static::text($data, 'title', $locale),
            'accent' => static::text($data, 'accent', $locale),
            'body' => $body,
            'headings' => $headings,
            'tocLabel' => static::text($data, 'toc_label', $locale) ?? __('front.contents', [], $locale),
            'aside' => static::text($data, 'aside', $locale),
        ];
    }

    /**
     * Gives every h2 an id and collects them for the table of contents.
     *
     * @param  array<int, array{id: string, text: string}>  $headings
     */
    private static function withHeadingIds(string $html, array &$headings): string
    {
        $index = 0;
        $collected = [];

        $html = preg_replace_callback(
            '#<h2\b([^>]*)>(.*?)</h2>#is',
            function (array $match) use (&$collected, &$index): string {
                $index++;
                $attributes = $match[1];
                $text = trim(html_entity_decode(strip_tags($match[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                if (preg_match('#\bid\s*=\s*["\']([^"\']+)["\']#i', $attributes, $existing)) {
                    $id = $existing[1];
                } else {
                    $id = Str::slug($text) ?: 'bolum-'.$index;
                    $attributes .= ' id="'.e($id).'"';
                }

                $collected[] = ['id' => $id, 'text' => $text];

                return '<h2'.$attributes.'>'.$match[2].'</h2>';
            },
            $html,
        ) ?? $html;

        $headings = $collected;

        return $html;
    }
}
