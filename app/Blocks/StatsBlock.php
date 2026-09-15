<?php

namespace App\Blocks;

use App\Filament\Support\LocaleTabs;
use App\Settings\SiteSettings;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

/**
 * The credentials strip: years of experience, patients, techniques and
 * publications. Left empty it falls back to the numbers in site settings.
 */
class StatsBlock extends AbstractBlock
{
    public static function key(): string
    {
        return 'stats';
    }

    public static function label(): string
    {
        return 'Güven şeridi (sayılar)';
    }

    public static function icon(): string
    {
        return 'heroicon-o-chart-bar';
    }

    public static function schema(): array
    {
        return [
            Toggle::make('use_settings')
                ->label('Site ayarlarındaki sayıları kullan')
                ->default(true)
                ->live(),

            Repeater::make('items')
                ->label('Sayılar')
                ->schema([
                    TextInput::make('value')
                        ->label('Sayı')
                        ->required()
                        ->maxLength(20),
                    LocaleTabs::make(fn (string $locale): array => [
                        TextInput::make("label.{$locale}")
                            ->label('Açıklama')
                            ->maxLength(60),
                    ]),
                ])
                ->defaultItems(0)
                ->collapsed()
                ->itemLabel(fn (array $state): ?string => $state['value'] ?? null)
                ->visible(fn (callable $get): bool => ! $get('use_settings')),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function transform(array $data, string $locale): array
    {
        if ($data['use_settings'] ?? true) {
            return ['items' => static::fromSettings($locale)];
        }

        return [
            'items' => array_map(
                fn (array $row): array => [
                    'value' => $row['value'] ?? null,
                    'label' => static::text($row, 'label', $locale),
                ],
                $data['items'] ?? [],
            ),
        ];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private static function fromSettings(string $locale): array
    {
        $settings = app(SiteSettings::class);
        $stats = $settings->stats;

        $labels = [
            'experience_years' => ['tr' => 'yıllık hekimlik deneyimi', 'en' => 'years of practice', 'ru' => 'лет практики', 'ar' => 'عامًا من الخبرة الطبية'],
            'patients' => ['tr' => 'hasta', 'en' => 'patients', 'ru' => 'пациентов', 'ar' => 'مريض'],
            'techniques' => ['tr' => 'girişimsel teknik', 'en' => 'interventional techniques', 'ru' => 'интервенционных методик', 'ar' => 'تقنية تداخلية'],
            'publications' => ['tr' => 'bilimsel yayın', 'en' => 'scientific publications', 'ru' => 'научных публикаций', 'ar' => 'منشورًا علميًا'],
        ];

        $items = [];

        foreach ($labels as $key => $translations) {
            if (! isset($stats[$key])) {
                continue;
            }

            $items[] = [
                'value' => number_format((int) $stats[$key], 0, ',', '.'),
                'label' => $translations[$locale] ?? $translations['tr'],
            ];
        }

        return $items;
    }
}
