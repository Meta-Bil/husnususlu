<?php

namespace App\Blocks;

use App\Filament\Support\LocaleTabs;
use App\Settings\SiteSettings;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

/**
 * The "Ekranlarda" strip: a label, a hairline and the names of the channels the
 * doctor has appeared on. Left alone it uses the channels from site settings.
 */
class MediaStripBlock extends AbstractBlock
{
    public static function key(): string
    {
        return 'media_strip';
    }

    public static function label(): string
    {
        return 'Ekranlarda şeridi';
    }

    public static function icon(): string
    {
        return 'heroicon-o-tv';
    }

    public static function schema(): array
    {
        return [
            LocaleTabs::make(fn (string $locale): array => [
                TextInput::make("label.{$locale}")
                    ->label('Etiket')
                    ->placeholder(__('front.on_screens', [], $locale))
                    ->maxLength(60),
            ]),

            Toggle::make('use_settings')
                ->label('Site ayarlarındaki kanalları kullan')
                ->default(true)
                ->live(),

            Repeater::make('channels')
                ->label('Kanallar')
                ->simple(
                    TextInput::make('name')
                        ->label('Kanal')
                        ->required()
                        ->maxLength(60),
                )
                ->defaultItems(0)
                ->visible(fn (callable $get): bool => ! $get('use_settings')),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function transform(array $data, string $locale): array
    {
        return [
            'label' => static::text($data, 'label', $locale) ?? __('front.on_screens', [], $locale),
            'channels' => static::channels($data),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, string>
     */
    private static function channels(array $data): array
    {
        if ($data['use_settings'] ?? true) {
            return array_values(array_filter(app(SiteSettings::class)->media_channels));
        }

        return array_values(array_filter(array_map(
            fn ($channel): string => is_array($channel) ? (string) ($channel['name'] ?? '') : (string) $channel,
            $data['channels'] ?? [],
        )));
    }
}
