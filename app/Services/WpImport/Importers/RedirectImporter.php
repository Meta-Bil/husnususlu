<?php

namespace App\Services\WpImport\Importers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Treatment;
use App\Support\Http\RedirectResolver;
use App\Support\Localization\LocaleUrls;
use Illuminate\Database\Eloquent\Model;

/**
 * Every address the old site answered on, pointed at its new home.
 *
 * Sources are Yoast's stored permalinks and each row's own slug, in both the
 * unprefixed Turkish form and the `/en/` form WPML served. Where a page and a
 * post shared a slug — the old site allowed it — the page keeps the address
 * and the collision is written into the report.
 */
class RedirectImporter extends Importer
{
    /**
     * Keyed by old path. A null target means the record still answers there,
     * so the path is reserved rather than redirected.
     *
     * @var array<string, array{to: string|null, note: string|null, owner: string}>
     */
    private array $redirects = [];

    /** @var array<int, string> */
    private array $collisions = [];

    public static function key(): string
    {
        return 'redirects';
    }

    public function import(): void
    {
        /* Pages claim their paths first so they win any collision. */
        $this->collect('page', Page::class, 'page');
        $this->collect('treatment', Treatment::class, 'treatment');
        $this->collect('post', Post::class, 'post');
        $this->collectRetiredPages();

        foreach ($this->collisions as $collision) {
            $this->context->report->note(self::key(), $collision);
        }

        $this->write();
    }

    public function fresh(): void
    {
        if ($this->context->dryRun) {
            return;
        }

        Redirect::query()->delete();
        RedirectResolver::flushCache();
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function collect(string $wpType, string $model, string $owner): void
    {
        $permalinks = $this->context->wp->permalinks();

        foreach ($this->context->mappedIds($wpType, $model) as $wpId => $recordId) {
            $record = $model::query()->find($recordId);

            if ($record === null) {
                continue;
            }

            foreach ($this->sourceIds($wpType, (int) $wpId) as $locale => $sourceId) {
                $target = LocaleUrls::model($record, $locale);

                if ($target === null) {
                    continue;
                }

                $target = $this->path($target);

                foreach ($this->oldPaths($sourceId, $locale, $permalinks) as $from) {
                    $this->add($from, $target, $owner);
                }
            }
        }
    }

    /**
     * The retired pages have no record of their own, so they point at the
     * closest page that replaced them.
     */
    private function collectRetiredPages(): void
    {
        $permalinks = $this->context->wp->permalinks();

        foreach ((array) config('wp-import.retired_pages', []) as $wpId => $to) {
            $post = $this->context->wp->post((int) $wpId);

            if ($post === null) {
                continue;
            }

            foreach ($this->oldPaths((int) $wpId, 'tr', $permalinks) as $from) {
                $this->add($from, (string) $to, 'retired', 'Eski sayfa aktarılmadı.');
            }

            $this->context->report->note(
                self::key(),
                "Aktarılmayan sayfa yönlendirildi: {$post->post_title} → {$to}",
            );
        }
    }

    /**
     * The WordPress rows behind one record, as `locale => wp id`.
     *
     * @return array<string, int>
     */
    private function sourceIds(string $wpType, int $wpId): array
    {
        $wanted = (array) config('wp-import.locales', []);
        $elementType = $wpType === 'post' ? 'post_post' : 'post_page';
        $sources = [];

        foreach ($this->context->wp->translationsOf($elementType, $wpId) as $language => $id) {
            if (isset($wanted[$language])) {
                $sources[$wanted[$language]] = $id;
            }
        }

        return $sources === [] ? ['tr' => $wpId] : $sources;
    }

    /**
     * Every address the old site could be reached on for one row.
     *
     * @param  array<int, string>  $permalinks
     * @return array<int, string>
     */
    private function oldPaths(int $wpId, string $locale, array $permalinks): array
    {
        $paths = [];

        if ($permalink = $permalinks[$wpId] ?? null) {
            $paths[] = $this->path($permalink);
        }

        $post = $this->context->wp->post($wpId);
        $slug = $post?->post_name;

        if (filled($slug)) {
            $slug = trim(rawurldecode((string) $slug), '/');

            /* WPML served the same page unprefixed and under `/en/`. */
            $paths[] = '/'.$slug;
            $paths[] = '/en/'.$slug;
            $paths[] = '/'.$locale.'/'.$slug;
        }

        return array_values(array_unique(array_filter($paths, fn (string $path): bool => $path !== '/')));
    }

    private function add(string $from, string $to, string $owner, ?string $note = null): void
    {
        $from = $this->normalize($from);

        if ($from === '' || $from === '/') {
            return;
        }

        $existing = $this->redirects[$from] ?? null;

        if ($existing !== null) {
            if ($existing['owner'] !== $owner && $existing['to'] !== $to) {
                $this->collisions[] = sprintf(
                    'Aynı adres iki kayıtta: %s — %s kazandı (%s), %s (%s) atlandı.',
                    $from,
                    $existing['owner'],
                    $existing['to'] ?? 'adres aynı kaldı',
                    $owner,
                    $to,
                );
            }

            return;
        }

        /* The record still answers on this address, so there is nothing to
           redirect — but the path is taken, and a later record must not claim
           it. Keeping the entry with an empty target reserves it. */
        $this->redirects[$from] = [
            'to' => $this->normalize($to) === $from ? null : $to,
            'note' => $note,
            'owner' => $owner,
        ];
    }

    private function write(): void
    {
        /* Drop the addresses that were only reserved. */
        $this->redirects = array_filter($this->redirects, fn (array $redirect): bool => $redirect['to'] !== null);

        if ($this->context->dryRun) {
            $this->context->report->created('redirects', count($this->redirects));

            return;
        }

        foreach ($this->redirects as $from => $redirect) {
            $existing = Redirect::query()->where('from_path', $from)->first();

            $record = $existing ?? new Redirect;
            $record->fill([
                'from_path' => $from,
                'to_path' => $redirect['to'],
                'status_code' => 301,
                'is_active' => true,
                'note' => $redirect['note'],
            ])->save();

            $existing ? $this->context->report->updated('redirects') : $this->context->report->created('redirects');
        }

        RedirectResolver::flushCache();
    }

    /**
     * The path part of an absolute URL, so a redirect never leaves the site.
     */
    private function path(string $url): string
    {
        if (! str_starts_with($url, 'http')) {
            return $this->normalize($url);
        }

        return $this->normalize((string) (parse_url($url, PHP_URL_PATH) ?: '/'));
    }

    /**
     * The shape {@see RedirectResolver} looks paths up in.
     */
    private function normalize(string $path): string
    {
        $path = '/'.trim(rawurldecode($path), '/');

        return mb_strtolower($path === '//' ? '/' : $path);
    }
}
