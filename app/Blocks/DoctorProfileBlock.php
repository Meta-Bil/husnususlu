<?php

namespace App\Blocks;

use App\Filament\Support\LocaleTabs;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Storage;

/**
 * The physician: a black and white portrait next to the name, the title, a short
 * paragraph, a table of credentials and two links.
 */
class DoctorProfileBlock extends AbstractBlock
{
    public static function key(): string
    {
        return 'doctor_profile';
    }

    public static function label(): string
    {
        return 'Hekim tanıtımı';
    }

    public static function icon(): string
    {
        return 'heroicon-o-user-circle';
    }

    public static function schema(): array
    {
        return [
            FileUpload::make('image')
                ->label('Portre')
                ->image()
                ->disk('public')
                ->directory('blocks/doctor')
                ->imageEditor(),

            LocaleTabs::make(fn (string $locale): array => [
                TextInput::make("eyebrow.{$locale}")->label('Üst etiket')->maxLength(80),
                TextInput::make("name.{$locale}")->label('Ad')->maxLength(120),
                TextInput::make("role.{$locale}")
                    ->label('Unvan')
                    ->placeholder(__('front.doctor_title', [], $locale))
                    ->maxLength(120),
                Textarea::make("text.{$locale}")->label('Metin')->rows(4),
            ]),

            Repeater::make('facts')
                ->label('Bilgi satırları')
                ->schema([
                    LocaleTabs::make(fn (string $locale): array => [
                        TextInput::make("label.{$locale}")->label('Başlık')->maxLength(80),
                        Textarea::make("value.{$locale}")->label('Değer')->rows(2),
                    ]),
                ])
                ->defaultItems(0)
                ->collapsed()
                ->itemLabel(fn (array $state): ?string => $state['label'][config('locales.default')] ?? null),

            Section::make('Bağlantılar')
                ->schema([
                    LocaleTabs::make(fn (string $locale): array => [
                        TextInput::make("primary_label.{$locale}")->label('Birinci bağlantı metni')->maxLength(80),
                        TextInput::make("secondary_label.{$locale}")->label('İkinci bağlantı metni')->maxLength(80),
                    ]),
                    TextInput::make('primary_url')->label('Birinci bağlantı adresi')->maxLength(255),
                    TextInput::make('secondary_url')->label('İkinci bağlantı adresi')->maxLength(255),
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
            'image' => filled($data['image'] ?? null) ? Storage::disk('public')->url($data['image']) : null,
            'eyebrow' => static::text($data, 'eyebrow', $locale),
            'name' => static::text($data, 'name', $locale) ?? config('app.name'),
            'role' => static::text($data, 'role', $locale) ?? __('front.doctor_title', [], $locale),
            'text' => static::text($data, 'text', $locale),
            'facts' => static::rows($data['facts'] ?? [], ['label', 'value'], $locale),
            'links' => array_values(array_filter([
                static::link(static::text($data, 'primary_label', $locale), $data['primary_url'] ?? null),
                static::link(static::text($data, 'secondary_label', $locale), $data['secondary_url'] ?? null),
            ])),
        ];
    }

    /**
     * @return array{label: string, url: string}|null
     */
    private static function link(?string $label, ?string $url): ?array
    {
        if (blank($label) || blank($url)) {
            return null;
        }

        return ['label' => $label, 'url' => $url];
    }
}
