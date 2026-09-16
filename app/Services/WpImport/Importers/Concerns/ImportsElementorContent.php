<?php

namespace App\Services\WpImport\Importers\Concerns;

use App\Services\WpImport\Elementor\BlockMerger;
use App\Services\WpImport\Support\TurkishText;
use App\Support\Localization\Locales;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

/**
 * The pipeline shared by pain types, procedures and editorial pages: find the
 * translations of a WordPress page, map each language's Elementor document and
 * fold them into one record.
 */
trait ImportsElementorContent
{
    /**
     * The WordPress ids of a page and its translations, as `locale => id`,
     * limited to the languages the new site serves.
     *
     * @return array<string, int>
     */
    protected function translations(int $wpId): array
    {
        $wanted = (array) config('wp-import.locales', []);
        $found = $this->context->wp->translationsOf('post_page', $wpId);
        $sources = [];

        foreach ($found as $language => $id) {
            if (isset($wanted[$language]) && $this->isPublished($id)) {
                $sources[$wanted[$language]] = $id;
            }
        }

        /* A page WPML never registered is still the Turkish original. */
        if ($sources === []) {
            $sources['tr'] = $wpId;
        }

        return $sources;
    }

    protected function isPublished(int $wpId): bool
    {
        $post = $this->context->wp->post($wpId);

        return $post !== null && $post->post_status === 'publish';
    }

    /**
     * Maps every language of a page and merges the result.
     *
     * @param  array<string, int>  $sources  locale => WordPress id
     * @return array{blocks: array<int, array<string, mixed>>, notes: array<int, string>, images: array<int, int>, locales: array<int, string>}
     */
    protected function blocksFor(array $sources): array
    {
        $mapped = [];
        $notes = [];
        $images = [];

        foreach ($sources as $locale => $wpId) {
            $result = $this->context->mapper($locale)->map($this->context->document($wpId));

            /* A page WordPress kept outside Elementor still has its content in
               `post_content`; only fall back when there is nothing else. */
            if ($result->isEmpty() && ($body = $this->postContentBody($wpId)) !== null) {
                $factory = $this->context->mapper($locale);
                $result = $result->withBlocks([['type' => 'rich_text', 'data' => [
                    'background' => 'paper',
                    'show_toc' => false,
                    'body' => [$locale => $body],
                ]]]);
                unset($factory);
            }

            $mapped[$locale] = $result->blocks;
            $images = array_merge($images, $result->imageIds);

            foreach ($result->notes as $note) {
                $notes[] = count($sources) > 1 ? strtoupper($locale).': '.$note : $note;
            }
        }

        $default = config('locales.default', 'tr');
        $primary = $mapped[$default] ?? reset($mapped) ?: [];
        $blocks = $primary;

        foreach ($mapped as $locale => $localeBlocks) {
            if ($localeBlocks === $primary) {
                continue;
            }

            $merged = BlockMerger::merge($blocks, $localeBlocks);
            $blocks = $merged['blocks'];

            if ($merged['unmatched'] > 0) {
                $notes[] = strtoupper($locale).': '.$merged['unmatched'].' blok Türkçe yapısıyla eşleşmedi ve alınmadı.';
            }
        }

        return [
            'blocks' => $blocks,
            'notes' => array_values(array_unique($notes)),
            'images' => array_values(array_unique($images)),
            'locales' => $this->orderLocales(
                array_keys(array_filter($mapped, fn (array $localeBlocks): bool => $localeBlocks !== [])),
            ),
        ];
    }

    /**
     * The site's own order, so the default language is always listed first.
     *
     * @param  array<int, string>  $locales
     * @return array<int, string>
     */
    protected function orderLocales(array $locales): array
    {
        return array_values(array_filter(
            Locales::codes(),
            fn (string $locale): bool => in_array($locale, $locales, true),
        ));
    }

    /**
     * Gutenberg content for the few pages that never went through Elementor.
     */
    protected function postContentBody(int $wpId): ?string
    {
        $post = $this->context->wp->post($wpId);

        if ($post === null || blank($post->post_content)) {
            return null;
        }

        $html = preg_replace('/<!--\s*\/?wp:[^>]*-->/', '', (string) $post->post_content) ?? (string) $post->post_content;
        $body = $this->context->sanitizer->clean($this->context->claims->scrub($html));

        return $this->context->sanitizer->text($body) === '' ? null : $body;
    }

    /**
     * @param  array<string, int>  $sources
     * @return array<string, string>
     */
    protected function localizedTitles(array $sources): array
    {
        $titles = [];

        foreach ($sources as $locale => $wpId) {
            $post = $this->context->wp->post($wpId);

            if ($post && filled($post->post_title)) {
                $titles[$locale] = $this->context->titles->fixTypos(TurkishText::normalize((string) $post->post_title));
            }
        }

        return $titles;
    }

    /**
     * @param  array<string, int>  $sources
     * @return array<string, string>
     */
    protected function localizedSlugs(array $sources): array
    {
        $slugs = [];

        foreach ($sources as $locale => $wpId) {
            $post = $this->context->wp->post($wpId);
            $slug = $post?->post_name;

            $slugs[$locale] = filled($slug)
                ? TurkishText::slug(rawurldecode((string) $slug))
                : TurkishText::slug((string) ($post->post_title ?? ''));
        }

        return array_filter($slugs);
    }

    /**
     * Yoast's title and description, with its template tags resolved.
     *
     * A description that was nothing but a promotional claim comes back empty
     * and is reported rather than stored, so the page falls back to its own
     * text instead of carrying a blank meta description.
     *
     * @param  array<string, int>  $sources
     * @return array{title: array<string, string>, description: array<string, string>, noindex: bool, notes: array<int, string>}
     */
    protected function seoFor(array $sources): array
    {
        $replacements = (array) config('wp-import.seo.replacements', []);
        $titles = [];
        $descriptions = [];
        $notes = [];
        $noindex = false;

        foreach ($sources as $locale => $wpId) {
            $row = $this->context->wp->yoast($wpId);

            if ($row === null) {
                continue;
            }

            foreach (['title' => &$titles, 'description' => &$descriptions] as $field => &$target) {
                if (blank($row->{$field})) {
                    continue;
                }

                $value = $this->expand((string) $row->{$field}, $replacements);

                if ($value === '') {
                    $notes[] = strtoupper($locale).': Yoast '.$field.' alanı tamamen reklam ifadesinden oluştuğu için alınmadı.';

                    continue;
                }

                $target[$locale] = $value;
            }

            unset($target);

            $noindex = $noindex || (bool) $row->is_robots_noindex;
        }

        return ['title' => $titles, 'description' => $descriptions, 'noindex' => $noindex, 'notes' => $notes];
    }

    /**
     * @param  array<string, string>  $replacements
     */
    protected function expand(string $value, array $replacements): string
    {
        $value = strtr(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'), $replacements);
        $value = $this->context->claims->scrub($value);
        $value = $this->context->titles->fixTypos($value);
        $value = preg_replace('/\s*–\s*(?=–|$)/u', '', $value) ?? $value;

        return trim(TurkishText::normalize($value), " \t–-|");
    }

    /**
     * The image to use as the record's cover: WordPress's featured image, or
     * else the first image the page used.
     *
     * @param  array<string, int>  $sources
     * @param  array<int, int>  $imageIds
     */
    protected function attachCover(Model&HasMedia $model, array $sources, array $imageIds): void
    {
        $candidates = [];

        foreach ($sources as $wpId) {
            if ($thumbnail = $this->context->wp->metaValue($wpId, '_thumbnail_id')) {
                $candidates[] = (int) $thumbnail;
            }
        }

        foreach ([...$candidates, ...$imageIds] as $attachmentId) {
            if ($this->context->media->attach($model, $attachmentId, 'cover')) {
                return;
            }
        }
    }

    /**
     * @param  array<int, string>  $notes
     */
    protected function noteText(array $notes): ?string
    {
        return $notes === [] ? null : implode("\n", $notes);
    }

    protected function publishedAt(int $wpId): ?string
    {
        $post = $this->context->wp->post($wpId);

        return $post && $post->post_date_gmt && ! str_starts_with((string) $post->post_date_gmt, '0000')
            ? (string) $post->post_date_gmt
            : ($post?->post_date ?: null);
    }
}
