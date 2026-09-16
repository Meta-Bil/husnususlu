<?php

namespace App\Services\WpImport\Importers;

use App\Enums\MenuItemType;
use App\Enums\TreatmentKind;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Treatment;
use App\Services\WpImport\Support\TurkishText;
use App\Support\Localization\LocaleUrls;
use Illuminate\Support\Facades\DB;

/**
 * The navigation.
 *
 * The header is the Turkish `ana-menu`, nested as it was, with the English
 * `main-menu` merged onto it through the WPML `trid` that pairs the two lists
 * item by item. The WPML language switcher entries are dropped: the new site
 * renders a switcher of its own.
 *
 * The two footer menus never existed on the old site and are built from the
 * records the import just created.
 */
class MenuImporter extends Importer
{
    /** @var array<string, array<string, int>> */
    private array $records = [];

    public static function key(): string
    {
        return 'menus';
    }

    public function import(): void
    {
        $this->records = [
            'page' => $this->context->mappedIds('page', Page::class),
            'treatment' => $this->context->mappedIds('treatment', Treatment::class),
            'post' => $this->context->mappedIds('post', Post::class),
            'post_category' => $this->context->mappedIds('post_category', PostCategory::class),
        ];

        $this->importHeader();
        $this->buildFooterTreatments();
        $this->buildFooterCorporate();
    }

    public function fresh(): void
    {
        if ($this->context->dryRun) {
            return;
        }

        Menu::query()->each(fn (Menu $menu) => $menu->delete());
        DB::table('wp_import_maps')->where('wp_type', 'menu_item')->delete();
    }

    private function importHeader(): void
    {
        $config = (array) config('wp-import.menus.header', []);
        $items = $this->context->wp->menuItems((string) ($config['source'] ?? 'ana-menu'));
        $translations = $this->translationLabels((string) ($config['merge'] ?? ''));

        if ($items->isEmpty()) {
            $this->context->report->warn(self::key(), 'Üst menü eski sitede bulunamadı.');

            return;
        }

        $menu = $this->menu('header', (string) ($config['name'] ?? 'Üst menü'));
        $parents = [];
        $order = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $wpId = (int) $item->ID;
            $meta = $this->itemMeta($wpId);

            if ($this->isLanguageSwitcher($meta)) {
                $skipped++;

                continue;
            }

            $labels = ['tr' => $this->labelFor($wpId, $meta, (string) $item->post_title)];

            if ($item->trid && isset($translations[(int) $item->trid])) {
                $labels['en'] = $translations[(int) $item->trid];
            }

            $target = $this->targetFor($meta, $labels);

            if ($target === null) {
                $skipped++;
                $this->context->report->note(self::key(), "Menü öğesi atlandı: {$labels['tr']}");

                continue;
            }

            $parentWpId = (int) ($meta['_menu_item_menu_item_parent'] ?? 0);

            $attributes = [
                'menu_id' => $menu?->id,
                'parent_id' => $parents[$parentWpId] ?? null,
                'label' => $labels,
                'sort_order' => ++$order,
                'is_visible' => true,
                ...$target,
            ];

            if ($this->context->dryRun) {
                $this->context->report->created('menu_items');

                continue;
            }

            $record = $this->context->mapped('menu_item', $wpId, MenuItem::class) ?? new MenuItem;
            $exists = $record->exists;
            $record->fill($attributes)->save();

            $this->context->remember('menu_item', $wpId, $record);
            $parents[$wpId] = $record->id;

            $exists ? $this->context->report->updated('menu_items') : $this->context->report->created('menu_items');
        }

        if ($skipped > 0) {
            $this->context->report->note(self::key(), "{$skipped} menü öğesi atlandı (dil seçici ya da hedefi olmayan bağlantı).");
            $this->context->report->skipped('menu_items', $skipped);
        }
    }

    /**
     * The English labels of the merged menu, keyed by the WPML group they
     * share with their Turkish twin.
     *
     * @return array<int, string>
     */
    private function translationLabels(string $menuSlug): array
    {
        if ($menuSlug === '') {
            return [];
        }

        $labels = [];

        foreach ($this->context->wp->menuItems($menuSlug) as $item) {
            if (! $item->trid) {
                continue;
            }

            $meta = $this->itemMeta((int) $item->ID);
            $label = $this->labelFor((int) $item->ID, $meta, (string) $item->post_title);

            if ($label !== '') {
                $labels[(int) $item->trid] = $label;
            }
        }

        return $labels;
    }

    /**
     * WordPress leaves a menu item's title empty when it should follow the
     * page it points at.
     *
     * @param  array<string, string>  $meta
     */
    private function labelFor(int $wpId, array $meta, string $title): string
    {
        $title = TurkishText::normalize(strip_tags($title));

        if ($title === '' && isset($meta['_menu_item_object_id'])) {
            $target = $this->context->wp->post((int) $meta['_menu_item_object_id']);
            $title = TurkishText::normalize(strip_tags((string) ($target->post_title ?? '')));
        }

        unset($wpId);

        return $this->context->titles->fixTypos($title);
    }

    /**
     * What the item links to, as columns of `menu_items`.
     *
     * @param  array<string, string>  $meta
     * @param  array<string, string>  $labels
     * @return array<string, mixed>|null
     */
    private function targetFor(array $meta, array $labels): ?array
    {
        $objectId = (int) ($meta['_menu_item_object_id'] ?? 0);
        $object = (string) ($meta['_menu_item_object'] ?? '');

        if ($object === 'page') {
            if ($id = $this->records['page'][$objectId] ?? null) {
                return ['type' => MenuItemType::Page, 'linkable_type' => Page::class, 'linkable_id' => $id, 'url' => null];
            }

            if ($id = $this->records['treatment'][$objectId] ?? null) {
                return ['type' => MenuItemType::Treatment, 'linkable_type' => Treatment::class, 'linkable_id' => $id, 'url' => null];
            }
        }

        if ($object === 'category' && ($id = $this->records['post_category'][$objectId] ?? null)) {
            return ['type' => MenuItemType::PostCategory, 'linkable_type' => PostCategory::class, 'linkable_id' => $id, 'url' => null];
        }

        /* A menu item pointing at a blog post: the schema links to pages,
           treatments and categories only, so it becomes a plain URL. */
        if ($object === 'post' && ($id = $this->records['post'][$objectId] ?? null)) {
            $post = Post::query()->find($id);

            if ($post && ($urls = $this->urlsFor($post)) !== []) {
                return ['type' => MenuItemType::Url, 'linkable_type' => null, 'linkable_id' => null, 'url' => $urls];
            }
        }

        $url = (string) ($meta['_menu_item_url'] ?? '');

        if ($url !== '' && ! $this->isLanguageSwitcher($meta)) {
            return ['type' => MenuItemType::Url, 'linkable_type' => null, 'linkable_id' => null, 'url' => ['tr' => $url]];
        }

        /* A parent that only opened a submenu keeps its place without a link. */
        return $labels['tr'] !== ''
            ? ['type' => MenuItemType::Url, 'linkable_type' => null, 'linkable_id' => null, 'url' => []]
            : null;
    }

    /**
     * @return array<string, string>
     */
    private function urlsFor(Post $post): array
    {
        $urls = [];

        foreach ($post->availableLocales() as $locale) {
            if ($url = LocaleUrls::post($post, $locale)) {
                $urls[$locale] = parse_url($url, PHP_URL_PATH) ?: $url;
            }
        }

        return $urls;
    }

    /**
     * @param  array<string, string>  $meta
     */
    private function isLanguageSwitcher(array $meta): bool
    {
        $url = trim((string) ($meta['_menu_item_url'] ?? ''));

        return $url !== '' && in_array($url, (array) config('wp-import.menu_item_url_blocklist', []), true);
    }

    /**
     * @return array<string, string>
     */
    private function itemMeta(int $wpId): array
    {
        return $this->context->wp->meta()[$wpId] ?? [];
    }

    /**
     * Pain types first, then the interventional procedures.
     */
    private function buildFooterTreatments(): void
    {
        $menu = $this->menu('footer_treatments', (string) config('wp-import.menus.footer_treatments.name', 'Alt menü — tedaviler'));

        $treatments = Treatment::query()
            ->orderByRaw('FIELD(kind, ?, ?)', [TreatmentKind::PainType->value, TreatmentKind::Procedure->value])
            ->orderBy('sort_order')
            ->get();

        $order = 0;

        foreach ($treatments as $treatment) {
            $this->footerItem($menu, $order++, (array) $treatment->title, [
                'type' => MenuItemType::Treatment,
                'linkable_type' => Treatment::class,
                'linkable_id' => $treatment->id,
            ]);
        }
    }

    /**
     * The corporate column: about, contact, blog, videos, the FAQ and the
     * legal page, in the order `config/wp-import.php` lists them.
     */
    private function buildFooterCorporate(): void
    {
        $menu = $this->menu('footer_corporate', (string) config('wp-import.menus.footer_corporate.name', 'Alt menü — kurumsal'));
        $order = 0;

        foreach ((array) config('wp-import.menus.footer_corporate.pages', []) as $wpId) {
            $pageId = $this->records['page'][(int) $wpId] ?? null;
            $page = $pageId ? Page::query()->find($pageId) : null;

            if ($page === null) {
                continue;
            }

            $this->footerItem($menu, $order++, (array) $page->title, [
                'type' => MenuItemType::Page,
                'linkable_type' => Page::class,
                'linkable_id' => $page->id,
            ]);
        }
    }

    /**
     * @param  array<string, string>  $label
     * @param  array<string, mixed>  $target
     */
    private function footerItem(?Menu $menu, int $order, array $label, array $target): void
    {
        if ($this->context->dryRun || $menu === null) {
            $this->context->report->created('menu_items');

            return;
        }

        $existing = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('linkable_type', $target['linkable_type'])
            ->where('linkable_id', $target['linkable_id'])
            ->first();

        $item = $existing ?? new MenuItem;
        $item->fill([
            'menu_id' => $menu->id,
            'parent_id' => null,
            'label' => $label,
            'url' => null,
            'sort_order' => $order + 1,
            'is_visible' => true,
            ...$target,
        ])->save();

        $existing ? $this->context->report->updated('menu_items') : $this->context->report->created('menu_items');
    }

    private function menu(string $key, string $name): ?Menu
    {
        if ($this->context->dryRun) {
            $this->context->report->created('menus');

            return null;
        }

        $menu = Menu::query()->firstOrNew(['key' => $key]);
        $exists = $menu->exists;
        $menu->fill(['name' => $name])->save();

        $exists ? $this->context->report->updated('menus') : $this->context->report->created('menus');

        return $menu;
    }
}
