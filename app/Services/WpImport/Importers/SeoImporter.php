<?php

namespace App\Services\WpImport\Importers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Treatment;
use App\Services\WpImport\Importers\Concerns\ImportsElementorContent;
use App\Services\WpImport\Support\TurkishText;
use Illuminate\Database\Eloquent\Model;

/**
 * Yoast's titles, descriptions and robots flags.
 *
 * Yoast stored its templates rather than the rendered text, so `%%sep%%` and
 * `%%sitename%%` are resolved here. Descriptions go through the same
 * promotional-claim filter as the body text: several of them advertised a
 * pain-free guarantee.
 */
class SeoImporter extends Importer
{
    use ImportsElementorContent;

    public static function key(): string
    {
        return 'seo';
    }

    public function import(): void
    {
        $this->applyTo('page', Page::class, 'pages');
        $this->applyTo('treatment', Treatment::class, 'treatments');
        $this->applyTo('post', Post::class, 'posts');
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function applyTo(string $wpType, string $model, string $kind): void
    {
        $elementType = $wpType === 'post' ? 'post_post' : 'post_page';

        foreach ($this->context->mappedIds($wpType, $model) as $wpId => $recordId) {
            $record = $model::query()->find($recordId);

            if ($record === null) {
                continue;
            }

            $sources = $this->sourcesFor($elementType, (int) $wpId);
            $seo = $this->seoFor($sources);
            $fallback = $this->fallbackTitles($record, $seo['title']);

            $attributes = [
                'seo_title' => $fallback,
                'seo_description' => $seo['description'] ?: (array) $record->seo_description,
                'noindex' => $seo['noindex'],
            ];

            if ($this->context->dryRun) {
                $this->context->report->updated("seo:{$kind}");

                continue;
            }

            $record->fill($attributes)->save();
            $this->context->report->updated("seo:{$kind}");
        }
    }

    /**
     * @return array<string, int>
     */
    private function sourcesFor(string $elementType, int $wpId): array
    {
        $wanted = (array) config('wp-import.locales', []);
        $sources = [];

        foreach ($this->context->wp->translationsOf($elementType, $wpId) as $language => $id) {
            if (isset($wanted[$language])) {
                $sources[$wanted[$language]] = $id;
            }
        }

        return $sources === [] ? ['tr' => $wpId] : $sources;
    }

    /**
     * Yoast left most pages without a title, so the record's own title plus
     * the practice name stands in.
     *
     * @param  array<string, string>  $titles
     * @return array<string, string>
     */
    private function fallbackTitles(Model $record, array $titles): array
    {
        $siteName = (string) config('wp-import.site_name');

        foreach ((array) $record->locales_enabled as $locale) {
            if (filled($titles[$locale] ?? null)) {
                continue;
            }

            $title = TurkishText::normalize((string) ($record->title[$locale] ?? ''));

            if ($title !== '') {
                $titles[$locale] = "{$title} – {$siteName}";
            }
        }

        return $titles;
    }
}
