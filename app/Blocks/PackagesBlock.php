<?php

namespace App\Blocks;

use App\Blocks\Concerns\InteractsWithContent;
use App\Filament\Support\LocaleTabs;
use App\Settings\SiteSettings;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

/**
 * Service packages for patients travelling from abroad: a card per package with
 * its feature list and a button.
 */
class PackagesBlock extends AbstractBlock
{
    use InteractsWithContent;

    public static function key(): string
    {
        return 'packages';
    }

    public static function label(): string
    {
        return 'Hizmet paketleri';
    }

    public static function icon(): string
    {
        return 'heroicon-o-briefcase';
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

            Repeater::make('items')
                ->label('Paketler')
                ->schema([
                    LocaleTabs::make(fn (string $locale): array => [
                        TextInput::make("eyebrow.{$locale}")->label('Üst etiket')->maxLength(60),
                        TextInput::make("title.{$locale}")
                            ->label('Paket adı')
                            ->required($locale === config('locales.default'))
                            ->maxLength(120),
                        Textarea::make("features.{$locale}")
                            ->label('Kapsam')
                            ->helperText('Her satır bir madde.')
                            ->rows(6),
                        TextInput::make("button_label.{$locale}")
                            ->label('Buton metni')
                            ->placeholder(__('front.write_on_whatsapp', [], $locale))
                            ->maxLength(60),
                    ]),
                    TextInput::make('button_url')
                        ->label('Buton adresi')
                        ->helperText('Boş bırakılırsa WhatsApp numarasına bağlanır.')
                        ->maxLength(255),
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
        $whatsapp = 'https://wa.me/'.preg_replace('/\D/', '', app(SiteSettings::class)->whatsapp_phone);
        $items = [];

        foreach ($data['items'] ?? [] as $index => $item) {
            $items[] = [
                'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'eyebrow' => static::text($item, 'eyebrow', $locale),
                'title' => static::text($item, 'title', $locale),
                'features' => static::lines(static::text($item, 'features', $locale)),
                'buttonLabel' => static::text($item, 'button_label', $locale) ?? __('front.write_on_whatsapp', [], $locale),
                'buttonUrl' => ($item['button_url'] ?? null) ?: $whatsapp,
            ];
        }

        return [
            'eyebrow' => static::text($data, 'eyebrow', $locale),
            'title' => static::text($data, 'title', $locale),
            'accent' => static::text($data, 'accent', $locale),
            'lead' => static::text($data, 'lead', $locale),
            'items' => $items,
        ];
    }
}
