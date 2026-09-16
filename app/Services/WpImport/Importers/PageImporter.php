<?php

namespace App\Services\WpImport\Importers;

use App\Enums\ContentStatus;
use App\Enums\PageTemplate;
use App\Models\Page;
use App\Services\WpImport\Importers\Concerns\ImportsElementorContent;
use Illuminate\Support\Facades\DB;

/**
 * The editorial pages: the home page, the about and contact pages, the FAQ,
 * the legal text and the section landing pages.
 *
 * Which WordPress page becomes which template is listed in
 * `config/wp-import.php`; pain types and procedures are handled by
 * {@see TreatmentImporter} and never reach this step.
 */
class PageImporter extends Importer
{
    use ImportsElementorContent;

    public static function key(): string
    {
        return 'pages';
    }

    public function import(): void
    {
        $treatments = (new TreatmentImporter($this->context))->index();
        $videos = (new VideoImporter($this->context))->index();

        if ($treatments === []) {
            $this->context->report->warn(
                self::key(),
                'Tedavi kayıtları yok; sayfalardaki tedavi listeleri bağlantı yerine metin olarak alındı.',
            );
        }

        if ($videos === []) {
            $this->context->report->warn(
                self::key(),
                'Video kayıtları yok; sayfalardaki video blokları belirli bir filme bağlanamadı.',
            );
        }

        $this->context->rememberTreatments($treatments);
        $this->context->rememberVideos($videos);

        $order = 0;

        foreach ((array) config('wp-import.pages', []) as $wpId => $template) {
            $this->importOne((int) $wpId, PageTemplate::from($template), ++$order);
        }
    }

    public function fresh(): void
    {
        if ($this->context->dryRun) {
            return;
        }

        Page::query()->withTrashed()->each(fn (Page $page) => $page->forceDelete());
        DB::table('wp_import_maps')->where('wp_type', 'page')->delete();
    }

    private function importOne(int $wpId, PageTemplate $template, int $order): void
    {
        $source = $this->context->wp->post($wpId);

        if ($source === null) {
            $this->context->report->warn(self::key(), "WordPress sayfası #{$wpId} bulunamadı.");
            $this->context->report->skipped('pages');

            return;
        }

        $sources = $this->translations($wpId);
        $content = $this->blocksFor($sources);
        $seo = $this->seoFor($sources);

        $notes = array_merge($content['notes'], $seo['notes']);
        $locales = $content['locales'] ?: $this->orderLocales(array_keys($sources));

        if ($content['blocks'] === [] && ! $this->mayBeEmpty($template)) {
            $notes[] = 'Eski sayfada aktarılabilir içerik bulunamadı.';
        }

        if ($template === PageTemplate::BlogIndex || $template === PageTemplate::Videos) {
            $notes[] = 'Liste sayfası: içerik kayıtlardan gelir, blok eklemek zorunlu değildir.';
        }

        $attributes = [
            'template' => $template,
            'title' => $this->localizedTitles($sources),
            'slug' => $this->slugs($sources, $template),
            'excerpt' => [],
            'blocks' => $content['blocks'],
            'seo_title' => $seo['title'],
            'seo_description' => $seo['description'],
            'schema_type' => $this->schemaType($template),
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
            $this->context->report->created('pages');

            return;
        }

        $page = $this->context->mapped('page', $wpId, Page::class) ?? new Page;
        $exists = $page->exists;

        $page->fill($attributes)->save();

        $this->context->remember('page', $wpId, $page);
        $this->attachCover($page, $sources, $content['images']);

        $exists ? $this->context->report->updated('pages') : $this->context->report->created('pages');
    }

    /**
     * The home page lives at the root of its locale but still needs a slug the
     * admin can show and the sitemap can key on.
     *
     * @param  array<string, int>  $sources
     * @return array<string, string>
     */
    private function slugs(array $sources, PageTemplate $template): array
    {
        $slugs = $this->localizedSlugs($sources);

        if ($template !== PageTemplate::Home) {
            return $slugs;
        }

        /* WordPress gave the English home page the slug `en`, which is the
           locale prefix rather than a page name. */
        foreach ($slugs as $locale => $slug) {
            if ($slug === $locale) {
                $slugs[$locale] = 'home';
            }
        }

        return $slugs;
    }

    private function schemaType(PageTemplate $template): string
    {
        return match ($template) {
            PageTemplate::Home => 'WebSite',
            PageTemplate::Contact => 'ContactPage',
            PageTemplate::BlogIndex => 'Blog',
            PageTemplate::Videos => 'CollectionPage',
            default => 'WebPage',
        };
    }

    private function mayBeEmpty(PageTemplate $template): bool
    {
        return in_array($template, [PageTemplate::BlogIndex, PageTemplate::Videos], true);
    }
}
