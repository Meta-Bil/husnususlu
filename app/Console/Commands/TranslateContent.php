<?php

namespace App\Console\Commands;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Treatment;
use App\Support\Localization\Locales;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Applies the reviewed translations kept in `database/translations` to the
 * content.
 *
 * The payload files are the source of truth and a human proofreads them, so the
 * command only copies: a record is matched by its Turkish slug (its Turkish
 * label, for menu items), model fields are filled per locale, and every
 * translatable leaf inside `blocks` is matched by its Turkish text. A value
 * that already has content is never touched, so a second run writes nothing;
 * `--force` replaces the ones the payload disagrees with.
 *
 * A record is published in a locale only once every Turkish value it holds has
 * been translated, which keeps a half-finished page out of its prefix, out of
 * the menus and out of the sitemap.
 */
#[Signature('translate:content
    {--locale=* : Locales to apply; defaults to every enabled locale but Turkish}
    {--model=* : Limit to pages, treatments, post_categories, posts or menu_items}
    {--dry-run : Report what would change without writing anything}
    {--force : Overwrite values that already have content}')]
#[Description('Applies the reviewed translations in database/translations to the content')]
class TranslateContent extends Command
{
    /**
     * The record models, in the order they are reported, with the field the
     * payload keys them by and the fields carried over from the payload.
     *
     * @var array<string, array{model: class-string<Model>, key: string, fields: array<int, string>}>
     */
    private const MODELS = [
        'pages' => [
            'model' => Page::class,
            'key' => 'slug',
            'fields' => ['title', 'slug', 'excerpt', 'seo_title', 'seo_description'],
        ],
        'treatments' => [
            'model' => Treatment::class,
            'key' => 'slug',
            'fields' => ['title', 'slug', 'summary', 'seo_title', 'seo_description'],
        ],
        'post_categories' => [
            'model' => PostCategory::class,
            'key' => 'slug',
            'fields' => ['name', 'slug', 'description'],
        ],
        'posts' => [
            'model' => Post::class,
            'key' => 'slug',
            'fields' => ['title', 'slug', 'excerpt', 'seo_title', 'seo_description'],
        ],
    ];

    /** Records whose translatable values are all filled are published in the locale. */
    private const PUBLISHES = ['pages', 'treatments', 'posts'];

    /** @var array<int, array{model: string, record: string, field: string, locale: string, value: string}> */
    private array $changes = [];

    /** @var array<int, string> */
    private array $missing = [];

    public function handle(): int
    {
        /* The long HTML bodies live in their own file so the hand-written
           dictionaries stay readable; both are matched by Turkish text. */
        $shared = array_replace($this->payload('shared'), $this->payload('bodies'));
        $locales = $this->locales();

        if ($locales === []) {
            $this->components->error('Çevrilecek dil bulunamadı.');

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->components->warn('Deneme çalışması: hiçbir kayıt yazılmayacak.');
        }

        $summary = [];

        foreach ($this->selectedModels() as $name) {
            $summary[] = $name === 'menu_items'
                ? $this->translateMenuItems($locales)
                : $this->translateRecords($name, self::MODELS[$name], $shared, $locales);
        }

        $this->report($summary);

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function selectedModels(): array
    {
        $available = [...array_keys(self::MODELS), 'menu_items'];
        $wanted = array_filter((array) $this->option('model'));

        if ($wanted === []) {
            return $available;
        }

        return array_values(array_intersect($available, $wanted));
    }

    /**
     * The locales asked for, limited to the ones the site serves so a payload
     * can carry a language before it is switched on.
     *
     * @return array<int, string>
     */
    private function locales(): array
    {
        $wanted = array_filter((array) $this->option('locale'));

        if ($wanted === []) {
            $wanted = array_values(array_diff(Locales::codes(), [Locales::default()]));
        }

        return array_values(array_intersect($wanted, Locales::enabled()));
    }

    /**
     * @param  array{model: class-string<Model>, key: string, fields: array<int, string>}  $definition
     * @param  array<string, array<string, string>>  $shared
     * @param  array<int, string>  $locales
     * @return array{0: string, 1: int, 2: int, 3: int}
     */
    private function translateRecords(string $name, array $definition, array $shared, array $locales): array
    {
        $payload = $this->payload($name);
        $model = $definition['model'];
        $touched = 0;
        $published = 0;

        /** @var Collection<int, Model> $records */
        $records = $model::query()->get();
        $keyed = $records->keyBy(fn (Model $record): string => (string) ($record->getAttribute($definition['key'])[Locales::default()] ?? ''));

        foreach ($payload as $key => $entry) {
            $record = $keyed->get((string) $key);

            if (! $record instanceof Model) {
                $this->missing[] = "{$name}: `{$key}` kaydı bulunamadı.";

                continue;
            }

            $dictionary = array_replace($shared, $entry['blocks'] ?? []);

            foreach ($locales as $locale) {
                $this->applyFields($record, $name, (string) $key, $definition['fields'], $entry['fields'] ?? [], $locale);
                $this->applyBlocks($record, $name, (string) $key, $dictionary, $entry['paths'] ?? [], $locale);
            }

            if ($record->isDirty()) {
                $touched++;
            }

            if (in_array($name, self::PUBLISHES, true)) {
                foreach ($locales as $locale) {
                    if ($this->publish($record, $name, (string) $key, $locale, $entry['publish'] ?? null)) {
                        $published++;
                    }
                }
            }

            $this->persist($record);
        }

        return [$name, count($payload), $touched, $published];
    }

    /**
     * @param  array<int, string>  $fields
     * @param  array<string, array<string, string>>  $translations
     */
    private function applyFields(Model $record, string $name, string $key, array $fields, array $translations, string $locale): void
    {
        foreach ($fields as $field) {
            $value = $translations[$field][$locale] ?? null;

            if (! is_string($value) || $value === '') {
                continue;
            }

            $values = (array) $record->getAttribute($field);

            if (! $this->replaceable($values[$locale] ?? null, $value)) {
                continue;
            }

            $values[$locale] = $value;
            $record->setAttribute($field, $values);
            $this->recordChange($name, $key, $field, $locale, $value);
        }
    }

    /**
     * Rewrites every translatable leaf of the `blocks` column whose Turkish text
     * the dictionary knows. A `paths` entry wins over the dictionary, which is
     * how one Turkish word gets two translations when it means two things in
     * two places on the same page.
     *
     * @param  array<string, array<string, string>>  $dictionary
     * @param  array<string, array<string, string>>  $paths
     */
    private function applyBlocks(Model $record, string $name, string $key, array $dictionary, array $paths, string $locale): void
    {
        $blocks = (array) $record->getAttribute('blocks');

        if ($blocks === []) {
            return;
        }

        $translated = $this->translateLeaves($blocks, $dictionary, $paths, $locale, $name, $key, 'blocks');

        if ($translated !== $blocks) {
            $record->setAttribute('blocks', $translated);
        }
    }

    /**
     * @param  array<array-key, mixed>  $node
     * @param  array<string, array<string, string>>  $dictionary
     * @param  array<string, array<string, string>>  $paths
     * @return array<array-key, mixed>
     */
    private function translateLeaves(array $node, array $dictionary, array $paths, string $locale, string $name, string $key, string $path): array
    {
        if ($this->isLeaf($node)) {
            $source = $node[Locales::default()] ?? null;

            if (! is_string($source) || $source === '') {
                return $node;
            }

            $value = $paths[$path][$locale] ?? $dictionary[$source][$locale] ?? null;

            if (! is_string($value) || $value === '') {
                return $node;
            }

            if (! $this->replaceable($node[$locale] ?? null, $value)) {
                return $node;
            }

            $node[$locale] = $value;
            $this->recordChange($name, $key, 'blocks', $locale, $value);

            return $node;
        }

        foreach ($node as $index => $child) {
            if (is_array($child)) {
                $node[$index] = $this->translateLeaves($child, $dictionary, $paths, $locale, $name, $key, $path.'.'.$index);
            }
        }

        return $node;
    }

    /**
     * A translatable leaf is a map of locale codes to strings, which is how both
     * the models and the block data store every translatable value.
     *
     * @param  array<array-key, mixed>  $value
     */
    private function isLeaf(array $value): bool
    {
        if ($value === []) {
            return false;
        }

        foreach ($value as $locale => $text) {
            if (! is_string($locale) || ! in_array($locale, Locales::codes(), true)) {
                return false;
            }

            if ($text !== null && ! is_string($text)) {
                return false;
            }
        }

        return true;
    }

    private function replaceable(?string $current, string $replacement): bool
    {
        if (blank($current)) {
            return true;
        }

        return $this->option('force') && $current !== $replacement;
    }

    /**
     * Publishes the record in a locale once every value it holds in Turkish has
     * been translated, so a half-translated page is never linked from a menu or
     * reachable under its prefix.
     */
    private function publish(Model $record, string $name, string $key, string $locale, ?bool $allowed): bool
    {
        $enabled = (array) $record->getAttribute('locales_enabled');

        if (in_array($locale, $enabled, true)) {
            return false;
        }

        if ($allowed === false || ! $this->isComplete($record, $locale)) {
            return false;
        }

        $enabled[] = $locale;
        $record->setAttribute('locales_enabled', array_values($enabled));
        $this->recordChange($name, $key, 'locales_enabled', $locale, $locale);

        return true;
    }

    private function isComplete(Model $record, string $locale): bool
    {
        /** @var array<int, string> $fields */
        $fields = method_exists($record, 'localizedAttributes') ? $record->localizedAttributes() : [];

        foreach ([...$fields, 'body', 'blocks'] as $field) {
            $value = $record->getAttribute($field);

            if (is_array($value) && ! $this->leavesAreFilled($value, $locale)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<array-key, mixed>  $node
     */
    private function leavesAreFilled(array $node, string $locale): bool
    {
        if ($this->isLeaf($node)) {
            return blank($node[Locales::default()] ?? null) || filled($node[$locale] ?? null);
        }

        foreach ($node as $child) {
            if (is_array($child) && ! $this->leavesAreFilled($child, $locale)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int, string>  $locales
     * @return array{0: string, 1: int, 2: int, 3: int}
     */
    private function translateMenuItems(array $locales): array
    {
        $labels = $this->payload('menu_items');
        $touched = 0;

        foreach (MenuItem::query()->get() as $item) {
            $values = (array) $item->label;
            $source = (string) ($values[Locales::default()] ?? '');

            if ($source === '') {
                continue;
            }

            if (! isset($labels[$source])) {
                $this->missing[] = "menu_items: `{$source}` etiketi çeviri dosyasında yok.";

                continue;
            }

            foreach ($locales as $locale) {
                $value = $labels[$source][$locale] ?? null;

                if (! is_string($value) || $value === '' || ! $this->replaceable($values[$locale] ?? null, $value)) {
                    continue;
                }

                $values[$locale] = $value;
                $this->recordChange('menu_items', $source, 'label', $locale, $value);
            }

            if ($values !== (array) $item->label) {
                $item->label = $values;
                $touched++;
            }

            $this->persist($item);
        }

        return ['menu_items', count($labels), $touched, 0];
    }

    private function persist(Model $record): void
    {
        if (! $record->isDirty() || $this->option('dry-run')) {
            return;
        }

        $record->save();
    }

    private function recordChange(string $model, string $record, string $field, string $locale, string $value): void
    {
        $this->changes[] = [
            'model' => $model,
            'record' => $record,
            'field' => $field,
            'locale' => $locale,
            'value' => $value,
        ];
    }

    /**
     * Reads one payload file. A missing file is not an error: the payload is
     * filled in model by model.
     *
     * @return array<string, mixed>
     */
    private function payload(string $name): array
    {
        $path = database_path("translations/{$name}.php");

        return file_exists($path) ? (array) require $path : [];
    }

    /**
     * @param  array<int, array{0: string, 1: int, 2: int, 3: int}>  $summary
     */
    private function report(array $summary): void
    {
        $this->newLine();
        $this->table(
            ['Kayıt', 'Çeviri dosyasında', 'Değişen kayıt', 'Yayına alınan dil'],
            $summary,
        );

        foreach (array_unique($this->missing) as $note) {
            $this->components->warn($note);
        }

        if ($this->changes === []) {
            $this->components->info('Her şey güncel; değişiklik yok.');

            return;
        }

        if ($this->output->isVerbose()) {
            $this->table(
                ['Kayıt', 'Alan', 'Dil', 'Değer'],
                array_map(fn (array $change): array => [
                    "{$change['model']}/{$change['record']}",
                    $change['field'],
                    $change['locale'],
                    mb_strimwidth($change['value'], 0, 60, '…'),
                ], $this->changes),
            );
        }

        $verb = $this->option('dry-run') ? 'yazılacak' : 'yazıldı';
        $this->components->info(count($this->changes)." değer {$verb}.");
    }
}
