<?php

namespace App\Services\WpImport\Importers;

use App\Enums\ContentStatus;
use App\Models\Post;
use App\Models\PostCategory;
use App\Services\WpImport\Elementor\ElementorNode;
use App\Services\WpImport\Importers\Concerns\ImportsElementorContent;
use App\Services\WpImport\Support\TurkishText;
use App\Services\WpImport\Support\YouTubeUrl;
use Illuminate\Support\Facades\DB;

/**
 * Blog posts and their categories.
 *
 * A post's body never lived in `post_content` — that column holds a rendered
 * copy of the old header. The article is inside the Elementor `text-editor`
 * widgets, and on the newest posts inside a hand-written `html` widget, both
 * of which are reduced to the handful of tags the new site renders.
 */
class PostImporter extends Importer
{
    use ImportsElementorContent;

    /** @var array<int, int>|null WordPress post id => its primary category term. */
    private ?array $primaryTerms = null;

    public static function key(): string
    {
        return 'posts';
    }

    public function import(): void
    {
        $categories = $this->importCategories();
        $relationships = $this->context->wp->termRelationships('category');
        $order = 0;

        foreach ($this->context->wp->published('post') as $row) {
            $this->importOne((int) $row->ID, $categories, $relationships, ++$order);
        }
    }

    public function fresh(): void
    {
        if ($this->context->dryRun) {
            return;
        }

        Post::query()->withTrashed()->each(fn (Post $post) => $post->forceDelete());
        PostCategory::query()->delete();
        DB::table('wp_import_maps')->whereIn('wp_type', ['post', 'post_category'])->delete();
    }

    /**
     * The blog categories, with the English name merged in through WPML.
     * WordPress's empty `Uncategorized` pair is left behind.
     *
     * @return array<int, int> WordPress term id => PostCategory id
     */
    private function importCategories(): array
    {
        $wanted = (array) config('wp-import.locales', []);
        $terms = $this->context->wp->terms('category');
        $byTrid = [];

        foreach ($terms as $term) {
            if ($term->trid) {
                $byTrid[(int) $term->trid][(string) $term->language_code] = $term;
            }
        }

        $ids = [];
        $order = 0;

        foreach ($terms as $term) {
            $termId = (int) $term->term_id;
            $language = (string) ($term->language_code ?: 'tr');

            /* Only the Turkish row of a translation group creates a record. */
            if ($term->trid && ($byTrid[(int) $term->trid]['tr'] ?? null) && $language !== 'tr') {
                continue;
            }

            if ((int) $term->count === 0) {
                $this->context->report->skipped('post_categories');

                continue;
            }

            $group = $term->trid ? ($byTrid[(int) $term->trid] ?? []) : [$language => $term];
            $names = [];
            $slugs = [];

            foreach ($group as $groupLanguage => $row) {
                if (! isset($wanted[$groupLanguage])) {
                    continue;
                }

                $locale = $wanted[$groupLanguage];
                $names[$locale] = $this->context->titles->fixTypos(TurkishText::normalize((string) $row->name));
                $slugs[$locale] = TurkishText::slug(rawurldecode((string) $row->slug));
            }

            if ($names === []) {
                $names['tr'] = $this->context->titles->fixTypos(TurkishText::normalize((string) $term->name));
                $slugs['tr'] = TurkishText::slug(rawurldecode((string) $term->slug));
            }

            $attributes = ['name' => $names, 'slug' => $slugs, 'sort_order' => ++$order, 'wp_id' => $termId];

            if ($this->context->dryRun) {
                $this->context->report->created('post_categories');

                continue;
            }

            $category = $this->context->mapped('post_category', $termId, PostCategory::class) ?? new PostCategory;
            $exists = $category->exists;
            $category->fill($attributes)->save();

            $this->context->remember('post_category', $termId, $category);

            foreach (array_keys($group) as $groupLanguage) {
                $groupTerm = $group[$groupLanguage];
                $this->context->remember('post_category', (int) $groupTerm->term_id, $category, $wanted[$groupLanguage] ?? null);
            }

            $ids[$termId] = $category->id;

            foreach ($group as $row) {
                $ids[(int) $row->term_id] = $category->id;
            }

            $exists ? $this->context->report->updated('post_categories') : $this->context->report->created('post_categories');
        }

        return $ids;
    }

    /**
     * @param  array<int, int>  $categories
     * @param  array<int, array<int, int>>  $relationships
     */
    private function importOne(int $wpId, array $categories, array $relationships, int $order): void
    {
        $source = $this->context->wp->post($wpId);

        if ($source === null) {
            return;
        }

        $sources = $this->postTranslations($wpId);
        $bodies = [];
        $notes = [];
        $images = [];
        $youtubeId = null;

        foreach ($sources as $locale => $localeWpId) {
            $article = $this->article($localeWpId);

            if ($article['body'] !== '') {
                $bodies[$locale] = $article['body'];
            }

            $notes = array_merge($notes, $article['notes']);
            $images = array_merge($images, $article['images']);
            $youtubeId ??= $article['youtube_id'];
        }

        if ($bodies === []) {
            $notes[] = 'Yazının gövdesi eski sitede bulunamadı.';
        }

        $seo = $this->seoFor($sources);
        $notes = array_merge($notes, $seo['notes']);
        $categoryId = $this->categoryFor($wpId, $categories, $relationships);
        $locales = $this->orderLocales(array_keys($bodies) ?: array_keys($sources));

        $attributes = [
            'post_category_id' => $categoryId,
            'title' => $this->localizedTitles($sources),
            'slug' => $this->localizedSlugs($sources),
            'excerpt' => $this->excerpts($bodies),
            'body' => $bodies,
            'video_youtube_id' => $youtubeId,
            'reading_time' => $this->readingTime($bodies),
            'seo_title' => $seo['title'],
            'seo_description' => $seo['description'],
            'noindex' => $seo['noindex'],
            'locales_enabled' => $locales,
            'status' => ContentStatus::Published,
            'published_at' => $this->publishedAt($wpId),
            'needs_review' => $notes !== [],
            'import_notes' => $this->noteText(array_values(array_unique($notes))),
            'wp_id' => $wpId,
        ];

        if ($this->context->dryRun) {
            $this->context->report->created('posts');

            return;
        }

        $post = $this->context->mapped('post', $wpId, Post::class) ?? new Post;
        $exists = $post->exists;

        $post->fill($attributes)->save();

        $this->context->remember('post', $wpId, $post);
        $this->attachCover($post, $sources, $images);

        $exists ? $this->context->report->updated('posts') : $this->context->report->created('posts');
        unset($order);
    }

    /**
     * Pulls the article out of the Elementor document.
     *
     * @return array{body: string, notes: array<int, string>, images: array<int, int>, youtube_id: string|null}
     */
    private function article(int $wpId): array
    {
        $document = $this->context->document($wpId);
        $chrome = $this->context->chrome();
        $parts = [];
        $notes = [];
        $images = [];
        $youtubeId = null;

        foreach ($document->widgets() as $widget) {
            if ($widget->is('video') && $youtubeId === null) {
                $youtubeId = YouTubeUrl::id($widget->stringSetting('youtube_url'));
            }

            if ($widget->is('image')) {
                $images[] = $widget->imageId();

                continue;
            }

            if (! $widget->is('text-editor', 'html')) {
                continue;
            }

            if ($reason = $chrome->reasonFor($widget)) {
                $notes[] = $this->skipNote($widget, $reason);

                continue;
            }

            if ($html = $this->fragment($widget, $notes)) {
                $parts[] = $html;
            }
        }

        return [
            'body' => $this->tidyBody(implode("\n", $parts)),
            'notes' => $notes,
            'images' => array_values(array_filter($images)),
            'youtube_id' => $youtubeId,
        ];
    }

    /**
     * @param  array<int, string>  $notes
     */
    private function fragment(ElementorNode $widget, array &$notes): ?string
    {
        $raw = (string) ($widget->stringSetting('editor') ?? $widget->stringSetting('html') ?? '');

        if (trim($raw) === '') {
            return null;
        }

        $before = $this->context->claims->removed();
        $scrubbed = $this->context->claims->scrub($raw);

        foreach (array_diff($this->context->claims->removed(), $before) as $claim) {
            $notes[] = 'Sağlık reklam kurallarına aykırı ifade çıkarıldı: "'.$claim.'"';
        }

        $html = $this->context->titles->fixTypos($this->context->sanitizer->clean($scrubbed));

        return $this->context->sanitizer->text($html) === '' ? null : $html;
    }

    /**
     * The first heading repeats the post title the template already prints.
     */
    private function tidyBody(string $body): string
    {
        return trim(preg_replace('#^\s*<h2\b[^>]*>.*?</h2>#is', '', $body, 1) ?? $body);
    }

    /**
     * WPML registered only one of the posts as translatable; the rest are
     * Turkish only.
     *
     * @return array<string, int>
     */
    private function postTranslations(int $wpId): array
    {
        $wanted = (array) config('wp-import.locales', []);
        $sources = [];

        foreach ($this->context->wp->translationsOf('post_post', $wpId) as $language => $id) {
            if (isset($wanted[$language]) && $this->isPublished($id)) {
                $sources[$wanted[$language]] = $id;
            }
        }

        return $sources === [] ? ['tr' => $wpId] : $sources;
    }

    /**
     * A post could sit in several WordPress categories; the new site gives it
     * one. Yoast recorded which was primary, so that choice is honoured.
     *
     * @param  array<int, int>  $categories
     * @param  array<int, array<int, int>>  $relationships
     */
    private function categoryFor(int $wpId, array $categories, array $relationships): ?int
    {
        $primary = $this->primaryTerms()[$wpId] ?? null;

        if ($primary !== null && isset($categories[$primary])) {
            return $categories[$primary];
        }

        foreach ($relationships[$wpId] ?? [] as $termId) {
            if (isset($categories[$termId])) {
                return $categories[$termId];
            }
        }

        return null;
    }

    /**
     * @return array<int, int>
     */
    private function primaryTerms(): array
    {
        return $this->primaryTerms ??= $this->context->wp->primaryTerms('category');
    }

    /**
     * @param  array<string, string>  $bodies
     * @return array<string, string>
     */
    private function excerpts(array $bodies): array
    {
        $excerpts = [];

        foreach ($bodies as $locale => $body) {
            $text = $this->context->sanitizer->text($body);

            if ($text === '') {
                continue;
            }

            $excerpt = mb_substr($text, 0, 200, 'UTF-8');
            $excerpts[$locale] = mb_strlen($text, 'UTF-8') > 200
                ? preg_replace('/\s+\S*$/u', '', $excerpt).'…'
                : $excerpt;
        }

        return $excerpts;
    }

    /**
     * @param  array<string, string>  $bodies
     */
    private function readingTime(array $bodies): ?int
    {
        $body = $bodies[config('locales.default', 'tr')] ?? reset($bodies);

        if (! is_string($body) || $body === '') {
            return null;
        }

        $words = count(preg_split('/\s+/u', $this->context->sanitizer->text($body)) ?: []);

        return max(1, (int) ceil($words / 200));
    }

    private function skipNote(ElementorNode $widget, string $reason): string
    {
        $text = $widget->text();

        return 'Atlandı: '.$widget->widgetType." ({$reason})"
            .($text === '' ? '' : ' — "'.mb_substr($text, 0, 60, 'UTF-8').'"');
    }
}
