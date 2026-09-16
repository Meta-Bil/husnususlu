<?php

namespace App\Services\WpImport\Importers;

use App\Enums\ContentStatus;
use App\Enums\TreatmentKind;
use App\Models\Treatment;
use App\Services\WpImport\Importers\Concerns\ImportsElementorContent;
use Illuminate\Support\Facades\DB;

/**
 * Pain types and interventional procedures.
 *
 * Both were ordinary WordPress pages on the old site; which page is which is
 * listed in `config/wp-import.php`. The English twin is found through WPML and
 * merged into the same record rather than becoming a second one.
 */
class TreatmentImporter extends Importer
{
    use ImportsElementorContent;

    public static function key(): string
    {
        return 'treatments';
    }

    public function import(): void
    {
        $this->context->rememberVideos((new VideoImporter($this->context))->index());

        $order = 0;

        foreach ((array) config('wp-import.treatments', []) as $kindValue => $wpIds) {
            $kind = TreatmentKind::from($kindValue);

            foreach ($wpIds as $wpId) {
                $this->importOne($kind, (int) $wpId, ++$order);
            }
        }

        $this->context->rememberTreatments($this->index());
    }

    public function fresh(): void
    {
        if ($this->context->dryRun) {
            return;
        }

        Treatment::query()->withTrashed()->each(fn (Treatment $treatment) => $treatment->forceDelete());
        DB::table('wp_import_maps')->where('wp_type', 'treatment')->delete();
    }

    /**
     * What the page mapper needs to turn card grids into links.
     *
     * @return array<int, array{id: int, kind: string, titles: array<int, string>}>
     */
    public function index(): array
    {
        return Treatment::query()
            ->get(['id', 'kind', 'title'])
            ->map(fn (Treatment $treatment): array => [
                'id' => $treatment->id,
                'kind' => $treatment->kind->value,
                'titles' => array_values(array_filter((array) $treatment->title)),
            ])
            ->all();
    }

    private function importOne(TreatmentKind $kind, int $wpId, int $order): void
    {
        $source = $this->context->wp->post($wpId);

        if ($source === null) {
            $this->context->report->warn(self::key(), "WordPress sayfası #{$wpId} bulunamadı.");
            $this->context->report->skipped('treatments');

            return;
        }

        $sources = $this->translations($wpId);
        $content = $this->blocksFor($sources);
        $seo = $this->seoFor($sources);
        $titles = $this->localizedTitles($sources);

        $notes = array_merge($content['notes'], $seo['notes']);
        $locales = $content['locales'] ?: $this->orderLocales(array_keys($sources));

        if (count($sources) === 1) {
            $notes[] = 'Bu kaydın İngilizcesi eski sitede yoktu.';
        }

        $attributes = [
            'kind' => $kind,
            'title' => $titles,
            'slug' => $this->localizedSlugs($sources),
            'summary' => $this->summaries($content['blocks'], $locales),
            'blocks' => $content['blocks'],
            'seo_title' => $seo['title'],
            'seo_description' => $seo['description'],
            'noindex' => $seo['noindex'],
            'locales_enabled' => $locales,
            'status' => ContentStatus::Published,
            'published_at' => $this->publishedAt($wpId),
            'sort_order' => $order,
            'needs_review' => $notes !== [],
            'import_notes' => $this->noteText($notes),
            'wp_id' => $wpId,
        ];

        if ($this->context->dryRun) {
            $this->context->report->created('treatments');

            return;
        }

        $treatment = $this->context->mapped('treatment', $wpId, Treatment::class) ?? new Treatment;
        $exists = $treatment->exists;

        $treatment->fill($attributes)->save();

        $this->context->remember('treatment', $wpId, $treatment);
        $this->attachCover($treatment, $sources, $content['images']);

        $exists ? $this->context->report->updated('treatments') : $this->context->report->created('treatments');
    }

    /**
     * The lead paragraph of the first block, used as the card summary.
     *
     * @param  array<int, array{type: string, data: array<string, mixed>}>  $blocks
     * @param  array<int, string>  $locales
     * @return array<string, string>
     */
    private function summaries(array $blocks, array $locales): array
    {
        $summaries = [];

        foreach ($locales as $locale) {
            foreach ($blocks as $block) {
                $source = $block['data']['lead'][$locale] ?? $block['data']['body'][$locale] ?? null;

                if (blank($source)) {
                    continue;
                }

                $text = $this->context->sanitizer->text((string) $source);

                if ($text === '') {
                    continue;
                }

                $summaries[$locale] = mb_substr($text, 0, 220, 'UTF-8');

                if (mb_strlen($text, 'UTF-8') > 220) {
                    $summaries[$locale] = preg_replace('/\s+\S*$/u', '', $summaries[$locale]).'…';
                }

                break;
            }
        }

        return $summaries;
    }
}
