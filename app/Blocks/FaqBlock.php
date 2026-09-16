<?php

namespace App\Blocks;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

/**
 * Frequently asked questions as a native accordion. The same questions are
 * contributed to the page as FAQPage structured data.
 */
class FaqBlock extends AbstractBlock
{
    public static function key(): string
    {
        return 'faq';
    }

    public static function label(): string
    {
        return 'Sıkça sorulan sorular';
    }

    public static function icon(): string
    {
        return 'heroicon-o-question-mark-circle';
    }

    public static function schema(): array
    {
        return [
            LocaleTabs::make(fn (string $locale): array => [
                TextInput::make("eyebrow.{$locale}")->label('Üst etiket')->maxLength(80),
                Textarea::make("title.{$locale}")->label('Başlık')->rows(2),
                TextInput::make("accent.{$locale}")->label('Vurgulu kelime (altın renkli)')->maxLength(80),
                Textarea::make("lead.{$locale}")->label('Giriş metni')->rows(3),
                TextInput::make("link_label.{$locale}")->label('Bağlantı metni')->maxLength(80),
            ]),

            TextInput::make('link_url')
                ->label('Bağlantı adresi')
                ->maxLength(255),

            Repeater::make('items')
                ->label('Sorular')
                ->schema([
                    LocaleTabs::make(fn (string $locale): array => [
                        TextInput::make("question.{$locale}")
                            ->label('Soru')
                            ->required($locale === config('locales.default'))
                            ->maxLength(200),
                        Textarea::make("answer.{$locale}")
                            ->label('Cevap')
                            ->rows(4),
                    ]),
                ])
                ->defaultItems(0)
                ->collapsed()
                ->itemLabel(fn (array $state): ?string => $state['question'][config('locales.default')] ?? null),
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
            'linkLabel' => static::text($data, 'link_label', $locale),
            'linkUrl' => $data['link_url'] ?? null,
            'items' => static::rows($data['items'] ?? [], ['question', 'answer'], $locale),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>|null
     */
    public static function jsonLd(array $data, string $locale): ?array
    {
        $questions = [];

        foreach (static::rows($data['items'] ?? [], ['question', 'answer'], $locale) as $item) {
            if (blank($item['question']) || blank($item['answer'])) {
                continue;
            }

            $questions[] = [
                '@type' => 'Question',
                'name' => $item['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $item['answer'],
                ],
            ];
        }

        if ($questions === []) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $questions,
        ];
    }
}
