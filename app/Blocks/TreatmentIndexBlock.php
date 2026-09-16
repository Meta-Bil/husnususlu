<?php

namespace App\Blocks;

use App\Blocks\Concerns\InteractsWithContent;
use App\Enums\TreatmentKind;
use App\Filament\Support\LocaleTabs;
use App\Models\Treatment;
use App\Support\Localization\LocaleUrls;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

/**
 * The numbered list of pain types in two columns, with the interventional
 * procedures underneath it as chips.
 */
class TreatmentIndexBlock extends AbstractBlock
{
    use InteractsWithContent;

    public static function key(): string
    {
        return 'treatment_index';
    }

    public static function label(): string
    {
        return 'Ağrı türleri listesi';
    }

    public static function icon(): string
    {
        return 'heroicon-o-list-bullet';
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

            Section::make('Ağrı türleri')
                ->schema([
                    Select::make('pain_type_ids')
                        ->label('Ağrı türleri')
                        ->helperText('Boş bırakılırsa yayındaki tüm ağrı türleri sırasıyla listelenir.')
                        ->options(fn (): array => static::treatmentOptions(TreatmentKind::PainType))
                        ->multiple()
                        ->searchable()
                        ->preload(),
                ])
                ->columns(1),

            Section::make('Girişimsel tedaviler')
                ->schema([
                    Toggle::make('show_procedures')
                        ->label('Girişimsel tedavileri göster')
                        ->default(true)
                        ->live(),
                    LocaleTabs::make(fn (string $locale): array => [
                        TextInput::make("procedures_label.{$locale}")->label('Etiket')->maxLength(60),
                    ]),
                    Select::make('procedure_ids')
                        ->label('Girişimsel tedaviler')
                        ->helperText('Boş bırakılırsa yayındaki tüm girişimsel tedaviler listelenir.')
                        ->options(fn (): array => static::treatmentOptions(TreatmentKind::Procedure))
                        ->multiple()
                        ->searchable()
                        ->preload(),
                ])
                ->columns(1)
                ->visible(fn (callable $get): bool => (bool) $get('show_procedures')),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function transform(array $data, string $locale): array
    {
        $painTypes = static::records(TreatmentKind::PainType, $data['pain_type_ids'] ?? [], $locale);

        $items = [];

        foreach ($painTypes as $index => $treatment) {
            $items[] = [
                'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'title' => $treatment->localized('title', $locale),
                'url' => LocaleUrls::treatment($treatment, $locale),
            ];
        }

        $procedures = ($data['show_procedures'] ?? true)
            ? static::records(TreatmentKind::Procedure, $data['procedure_ids'] ?? [], $locale)
            : [];

        return [
            'eyebrow' => static::text($data, 'eyebrow', $locale),
            'title' => static::text($data, 'title', $locale),
            'accent' => static::text($data, 'accent', $locale),
            'lead' => static::text($data, 'lead', $locale),
            'items' => $items,
            'proceduresLabel' => static::text($data, 'procedures_label', $locale) ?? __('front.treatments', [], $locale),
            'procedures' => array_map(fn (Treatment $treatment): array => [
                'title' => $treatment->localized('title', $locale),
                'url' => LocaleUrls::treatment($treatment, $locale),
            ], $procedures),
        ];
    }

    /**
     * The picked records, or every live one of that kind when nothing is picked.
     *
     * @param  array<int, int|string>  $ids
     * @return array<int, Treatment>
     */
    private static function records(TreatmentKind $kind, array $ids, string $locale): array
    {
        if (filled($ids)) {
            return static::treatmentsByIds($ids)
                ->filter(fn (Treatment $treatment): bool => $treatment->kind === $kind)
                ->values()
                ->all();
        }

        return Treatment::query()
            ->where('kind', $kind)
            ->live($locale)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->all();
    }
}
