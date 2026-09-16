<?php

namespace App\Blocks;

use App\Blocks\Concerns\InteractsWithContent;
use App\Enums\TreatmentKind;
use App\Filament\Support\LocaleTabs;
use App\Models\Treatment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Collection;

/**
 * Three treatment cards, either hand-picked or taken automatically from the
 * chosen kind.
 */
class RelatedTreatmentsBlock extends AbstractBlock
{
    use InteractsWithContent;

    public static function key(): string
    {
        return 'related_treatments';
    }

    public static function label(): string
    {
        return 'İlgili tedaviler';
    }

    public static function icon(): string
    {
        return 'heroicon-o-squares-2x2';
    }

    public static function schema(): array
    {
        return [
            LocaleTabs::make(fn (string $locale): array => [
                TextInput::make("eyebrow.{$locale}")->label('Üst etiket')->maxLength(80),
                Textarea::make("title.{$locale}")
                    ->label('Başlık')
                    ->placeholder(__('front.related_treatments', [], $locale))
                    ->rows(2),
                TextInput::make("accent.{$locale}")->label('Vurgulu kelime (altın renkli)')->maxLength(80),
                TextInput::make("link_label.{$locale}")->label('Bağlantı metni')->maxLength(80),
            ]),

            TextInput::make('link_url')
                ->label('Bağlantı adresi')
                ->maxLength(255),

            Section::make('Tedaviler')
                ->schema([
                    Select::make('source')
                        ->label('Kaynak')
                        ->options([
                            'kind' => 'Türe göre otomatik',
                            'selected' => 'Elle seçilen tedaviler',
                        ])
                        ->default('kind')
                        ->required()
                        ->live(),
                    Select::make('kind')
                        ->label('Tür')
                        ->options(TreatmentKind::class)
                        ->default(TreatmentKind::Procedure->value)
                        ->visible(fn (callable $get): bool => $get('source') !== 'selected'),
                    TextInput::make('limit')
                        ->label('Kart sayısı')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(12)
                        ->default(3)
                        ->visible(fn (callable $get): bool => $get('source') !== 'selected'),
                    Select::make('treatment_ids')
                        ->label('Tedaviler')
                        ->options(fn (): array => static::treatmentOptions())
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->visible(fn (callable $get): bool => $get('source') === 'selected'),
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
        $items = [];

        foreach (static::treatments($data, $locale) as $index => $treatment) {
            $items[] = static::treatmentCard($treatment, $locale) + [
                'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
            ];
        }

        return [
            'eyebrow' => static::text($data, 'eyebrow', $locale),
            'title' => static::text($data, 'title', $locale) ?? __('front.related_treatments', [], $locale),
            'accent' => static::text($data, 'accent', $locale),
            'linkLabel' => static::text($data, 'link_label', $locale),
            'linkUrl' => $data['link_url'] ?? null,
            'detailsLabel' => __('front.details', [], $locale),
            'items' => $items,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return Collection<int, Treatment>
     */
    private static function treatments(array $data, string $locale): Collection
    {
        if (($data['source'] ?? 'kind') === 'selected') {
            return static::treatmentsByIds($data['treatment_ids'] ?? []);
        }

        return Treatment::query()
            ->live($locale)
            ->when(
                filled($data['kind'] ?? null),
                fn ($query) => $query->where('kind', $data['kind']),
            )
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(max(1, (int) ($data['limit'] ?? 3)))
            ->get();
    }
}
