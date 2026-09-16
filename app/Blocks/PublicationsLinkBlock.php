<?php

namespace App\Blocks;

use App\Filament\Support\LocaleTabs;
use App\Settings\SiteSettings;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

/**
 * A small academic band pointing at the Google Scholar profile; the site has no
 * publications page of its own.
 */
class PublicationsLinkBlock extends AbstractBlock
{
    public static function key(): string
    {
        return 'publications_link';
    }

    public static function label(): string
    {
        return 'Akademik yayınlar bağlantısı';
    }

    public static function icon(): string
    {
        return 'heroicon-o-book-open';
    }

    public static function schema(): array
    {
        return [
            LocaleTabs::make(fn (string $locale): array => [
                TextInput::make("eyebrow.{$locale}")->label('Üst etiket')->maxLength(80),
                TextInput::make("title.{$locale}")
                    ->label('Başlık')
                    ->maxLength(160),
                Textarea::make("lead.{$locale}")->label('Metin')->rows(2),
                TextInput::make("link_label.{$locale}")
                    ->label('Bağlantı metni')
                    ->placeholder(__('front.publications', [], $locale))
                    ->maxLength(80),
            ]),

            TextInput::make('link_url')
                ->label('Bağlantı adresi')
                ->helperText('Boş bırakılırsa site ayarlarındaki Google Scholar adresi kullanılır.')
                ->url()
                ->maxLength(255),
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
            'lead' => static::text($data, 'lead', $locale),
            'linkLabel' => static::text($data, 'link_label', $locale) ?? __('front.publications', [], $locale),
            'linkUrl' => ($data['link_url'] ?? null) ?: (app(SiteSettings::class)->scholar_url ?: null),
        ];
    }
}
