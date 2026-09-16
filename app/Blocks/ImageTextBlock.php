<?php

namespace App\Blocks;

use App\Blocks\Concerns\InteractsWithContent;
use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Storage;

/**
 * A photograph on one side and editorial text on the other; which side the image
 * takes is up to the editor.
 */
class ImageTextBlock extends AbstractBlock
{
    use InteractsWithContent;

    public static function key(): string
    {
        return 'image_text';
    }

    public static function label(): string
    {
        return 'Görsel + metin';
    }

    public static function icon(): string
    {
        return 'heroicon-o-photo';
    }

    public static function schema(): array
    {
        return [
            FileUpload::make('image')
                ->label('Görsel')
                ->image()
                ->disk('public')
                ->directory('blocks/image-text')
                ->imageEditor(),

            Select::make('image_side')
                ->label('Görselin yeri')
                ->options([
                    'start' => 'Metnin başında',
                    'end' => 'Metnin sonunda',
                ])
                ->default('start')
                ->required(),

            Select::make('background')
                ->label('Zemin')
                ->options([
                    'paper' => 'Açık (kağıt)',
                    'ivory' => 'Açık (fildişi)',
                    'navy' => 'Koyu (lacivert)',
                ])
                ->default('paper')
                ->required(),

            LocaleTabs::make(fn (string $locale): array => [
                TextInput::make("eyebrow.{$locale}")->label('Üst etiket')->maxLength(80),
                Textarea::make("title.{$locale}")->label('Başlık')->rows(2),
                TextInput::make("accent.{$locale}")->label('Vurgulu kelime (altın renkli)')->maxLength(80),
                RichEditor::make("body.{$locale}")->label('Metin'),
                TextInput::make("link_label.{$locale}")->label('Bağlantı metni')->maxLength(80),
                TextInput::make("image_alt.{$locale}")->label('Görsel açıklaması')->maxLength(160),
            ]),

            TextInput::make('link_url')
                ->label('Bağlantı adresi')
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
            'image' => filled($data['image'] ?? null) ? Storage::disk('public')->url($data['image']) : null,
            'imageAlt' => static::text($data, 'image_alt', $locale) ?? static::text($data, 'title', $locale),
            'imageSide' => $data['image_side'] ?? 'start',
            'background' => $data['background'] ?? 'paper',
            'eyebrow' => static::text($data, 'eyebrow', $locale),
            'title' => static::text($data, 'title', $locale),
            'accent' => static::text($data, 'accent', $locale),
            'body' => static::sanitizeHtml(static::text($data, 'body', $locale)),
            'linkLabel' => static::text($data, 'link_label', $locale),
            'linkUrl' => $data['link_url'] ?? null,
        ];
    }
}
