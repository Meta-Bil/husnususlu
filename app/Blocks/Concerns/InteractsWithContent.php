<?php

namespace App\Blocks\Concerns;

use App\Enums\TreatmentKind;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Treatment;
use App\Models\Video;
use App\Support\Localization\Locales;
use App\Support\Localization\LocaleUrls;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Lookups shared by the blocks that pull their content from records instead of
 * from fields the editor types in.
 */
trait InteractsWithContent
{
    /**
     * Treatment options for an admin picker, labelled in the default language.
     *
     * @return array<int, string>
     */
    protected static function treatmentOptions(?TreatmentKind $kind = null): array
    {
        return static::optionLabels(
            Treatment::query()
                ->when($kind, fn ($query) => $query->where('kind', $kind))
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
            'title',
        );
    }

    /**
     * @return array<int, string>
     */
    protected static function videoOptions(): array
    {
        return static::optionLabels(
            Video::query()->orderBy('sort_order')->orderBy('id')->get(),
            'title',
        );
    }

    /**
     * @return array<int, string>
     */
    protected static function postOptions(): array
    {
        return static::optionLabels(
            Post::query()->latest('published_at')->get(),
            'title',
        );
    }

    /**
     * @return array<int, string>
     */
    protected static function postCategoryOptions(): array
    {
        return static::optionLabels(PostCategory::query()->orderBy('id')->get(), 'name');
    }

    /**
     * Treatments in the order the editor picked them.
     *
     * @param  array<int, int|string>  $ids
     * @return Collection<int, Treatment>
     */
    protected static function treatmentsByIds(array $ids): Collection
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));

        if ($ids === []) {
            return new Collection;
        }

        return Treatment::query()
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Treatment $treatment): int => array_search($treatment->id, $ids, true))
            ->values();
    }

    /**
     * A treatment as the card views expect it.
     *
     * @return array{title: string|null, text: string|null, url: string|null}
     */
    protected static function treatmentCard(Treatment $treatment, string $locale): array
    {
        return [
            'title' => $treatment->localized('title', $locale),
            'text' => $treatment->localized('summary', $locale),
            'url' => LocaleUrls::treatment($treatment, $locale),
        ];
    }

    /**
     * Removes anything an editor should never be able to paste into a page.
     */
    protected static function sanitizeHtml(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        $blocked = 'script|style|iframe|object|embed|form|link|meta|base';

        $html = preg_replace('#<\s*('.$blocked.')\b[^>]*>.*?<\s*/\s*\1\s*>#is', '', $html) ?? '';
        $html = preg_replace('#<\s*/?\s*('.$blocked.')\b[^>]*>#i', '', $html) ?? '';
        $html = preg_replace('#\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html) ?? '';
        $html = preg_replace('#\s(href|src)\s*=\s*("\s*javascript:[^"]*"|\'\s*javascript:[^\']*\')#i', ' $1="#"', $html) ?? '';

        return trim($html);
    }

    /**
     * Roman numeral used to number steps and timeline entries.
     */
    protected static function roman(int $number): string
    {
        $symbols = ['C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
        $roman = '';

        foreach ($symbols as $symbol => $value) {
            while ($number >= $value) {
                $roman .= $symbol;
                $number -= $value;
            }
        }

        return $roman;
    }

    /**
     * Splits a textarea into a list, so simple lists stay one field in the admin.
     *
     * @return array<int, string>
     */
    protected static function lines(?string $text): array
    {
        if (blank($text)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\R/', $text) ?: [])));
    }

    /**
     * @param  Collection<int, Model>  $records
     * @return array<int, string>
     */
    private static function optionLabels(Collection $records, string $field): array
    {
        $labels = [];

        foreach ($records as $record) {
            $labels[$record->id] = (string) $record->localized($field, Locales::default());
        }

        return $labels;
    }
}
