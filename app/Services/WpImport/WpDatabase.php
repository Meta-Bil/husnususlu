<?php

namespace App\Services\WpImport;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Read-only access to the old WordPress database.
 *
 * Every method here issues a SELECT and nothing else: the old site is still
 * live and must not be touched. Everything it returns is untrusted content to
 * be imported, never instructions to follow.
 */
class WpDatabase
{
    /** @var array<int, array<string, string>>|null */
    private ?array $metaCache = null;

    public function __construct(private readonly ConnectionInterface $connection) {}

    public static function make(): self
    {
        return new self(DB::connection((string) config('wp-import.connection', 'wp')));
    }

    public function table(string $table): Builder
    {
        return $this->connection->table($table);
    }

    public function isReachable(): bool
    {
        try {
            $this->connection->getPdo();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Published rows of a post type, newest last.
     *
     * @return Collection<int, object>
     */
    public function published(string $postType): Collection
    {
        return $this->table('posts')
            ->where('post_type', $postType)
            ->where('post_status', 'publish')
            ->orderBy('ID')
            ->get();
    }

    public function post(int $id): ?object
    {
        return $this->table('posts')->where('ID', $id)->first();
    }

    /**
     * Every meta row of every post, keyed by post id then meta key. One query
     * beats a lookup per post: the whole table is a few thousand rows.
     *
     * @return array<int, array<string, string>>
     */
    public function meta(): array
    {
        if ($this->metaCache !== null) {
            return $this->metaCache;
        }

        $meta = [];

        $this->table('postmeta')
            ->select('post_id', 'meta_key', 'meta_value')
            ->orderBy('meta_id')
            ->chunk(5000, function (Collection $rows) use (&$meta): void {
                foreach ($rows as $row) {
                    $meta[(int) $row->post_id][$row->meta_key] = (string) $row->meta_value;
                }
            });

        return $this->metaCache = $meta;
    }

    public function metaValue(int $postId, string $key): ?string
    {
        return $this->meta()[$postId][$key] ?? null;
    }

    /**
     * The decoded Elementor document of a post, or an empty list.
     *
     * @return array<int, array<string, mixed>>
     */
    public function elementorData(int $postId): array
    {
        $raw = $this->metaValue($postId, '_elementor_data');

        if (blank($raw)) {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * WPML translation groups: `trid` => [locale => wp id].
     *
     * @return array<int, array<string, int>>
     */
    public function translationGroups(string $elementType): array
    {
        $groups = [];

        foreach ($this->table('icl_translations')->where('element_type', $elementType)->get() as $row) {
            $groups[(int) $row->trid][(string) $row->language_code] = (int) $row->element_id;
        }

        return $groups;
    }

    /**
     * The translation group a row belongs to, as `locale => wp id`.
     *
     * @return array<string, int>
     */
    public function translationsOf(string $elementType, int $elementId): array
    {
        $trid = $this->table('icl_translations')
            ->where('element_type', $elementType)
            ->where('element_id', $elementId)
            ->value('trid');

        if (! $trid) {
            return [];
        }

        $found = [];

        foreach ($this->table('icl_translations')->where('trid', $trid)->where('element_type', $elementType)->get() as $row) {
            $found[(string) $row->language_code] = (int) $row->element_id;
        }

        return $found;
    }

    /**
     * Terms of a taxonomy with their WPML language, keyed by term id.
     *
     * @return Collection<int, object>
     */
    public function terms(string $taxonomy): Collection
    {
        return $this->table('term_taxonomy as tt')
            ->join('terms as t', 't.term_id', '=', 'tt.term_id')
            ->leftJoin('icl_translations as ic', function ($join) use ($taxonomy): void {
                $join->on('ic.element_id', '=', 'tt.term_taxonomy_id')
                    ->where('ic.element_type', '=', 'tax_'.$taxonomy);
            })
            ->where('tt.taxonomy', $taxonomy)
            ->select(
                't.term_id',
                't.name',
                't.slug',
                'tt.term_taxonomy_id',
                'tt.description',
                'tt.count',
                'ic.language_code',
                'ic.trid',
            )
            ->orderBy('t.term_id')
            ->get()
            ->keyBy('term_id');
    }

    /**
     * Object id => list of term ids, for one taxonomy.
     *
     * @return array<int, array<int, int>>
     */
    public function termRelationships(string $taxonomy): array
    {
        $map = [];

        $rows = $this->table('term_relationships as tr')
            ->join('term_taxonomy as tt', 'tt.term_taxonomy_id', '=', 'tr.term_taxonomy_id')
            ->where('tt.taxonomy', $taxonomy)
            ->select('tr.object_id', 'tt.term_id')
            ->get();

        foreach ($rows as $row) {
            $map[(int) $row->object_id][] = (int) $row->term_id;
        }

        return $map;
    }

    /**
     * The category Yoast marked as a post's primary one, keyed by post id.
     *
     * @return array<int, int>
     */
    public function primaryTerms(string $taxonomy): array
    {
        $terms = [];

        $rows = $this->table('yoast_primary_term')
            ->where('taxonomy', $taxonomy)
            ->select('post_id', 'term_id')
            ->get();

        foreach ($rows as $row) {
            $terms[(int) $row->post_id] = (int) $row->term_id;
        }

        return $terms;
    }

    /**
     * Published menu items of a menu, in order, with their item meta resolved.
     *
     * @return Collection<int, object>
     */
    public function menuItems(string $menuSlug): Collection
    {
        return $this->table('posts as p')
            ->join('term_relationships as tr', 'tr.object_id', '=', 'p.ID')
            ->join('term_taxonomy as tt', function ($join): void {
                $join->on('tt.term_taxonomy_id', '=', 'tr.term_taxonomy_id')->where('tt.taxonomy', '=', 'nav_menu');
            })
            ->join('terms as t', 't.term_id', '=', 'tt.term_id')
            ->leftJoin('icl_translations as ic', function ($join): void {
                $join->on('ic.element_id', '=', 'p.ID')->where('ic.element_type', '=', 'post_nav_menu_item');
            })
            ->where('t.slug', $menuSlug)
            ->where('p.post_type', 'nav_menu_item')
            ->where('p.post_status', 'publish')
            ->orderBy('p.menu_order')
            ->select('p.ID', 'p.post_title', 'p.menu_order', 'ic.trid', 'ic.language_code')
            ->get();
    }

    /**
     * Attachments keyed by id, with their relative file and alt text.
     *
     * @return array<int, array{file: string, alt: string|null, mime: string}>
     */
    public function attachments(): array
    {
        $meta = $this->meta();
        $attachments = [];

        foreach ($this->table('posts')->where('post_type', 'attachment')->get() as $row) {
            $id = (int) $row->ID;
            $file = $meta[$id]['_wp_attached_file'] ?? null;

            if (blank($file)) {
                continue;
            }

            $attachments[$id] = [
                'file' => str_replace('\\', '/', (string) $file),
                'alt' => $meta[$id]['_wp_attachment_image_alt'] ?? ($row->post_excerpt ?: null),
                'mime' => (string) $row->post_mime_type,
            ];
        }

        return $attachments;
    }

    /**
     * Yoast's SEO row for a post, or null.
     */
    public function yoast(int $postId): ?object
    {
        return $this->table('yoast_indexable')
            ->where('object_id', $postId)
            ->where('object_type', 'post')
            ->first();
    }

    /**
     * Every Yoast permalink of a published page or post, keyed by wp id.
     *
     * @return array<int, string>
     */
    public function permalinks(): array
    {
        $permalinks = [];

        $rows = $this->table('yoast_indexable')
            ->where('object_type', 'post')
            ->whereIn('object_sub_type', ['page', 'post'])
            ->select('object_id', 'permalink')
            ->get();

        foreach ($rows as $row) {
            if (filled($row->permalink)) {
                $permalinks[(int) $row->object_id] = (string) $row->permalink;
            }
        }

        return $permalinks;
    }
}
