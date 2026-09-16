<?php

namespace App\Services\WpImport;

use App\Models\WpImportMap;
use App\Services\WpImport\Elementor\BlockFactory;
use App\Services\WpImport\Elementor\BlockMapper;
use App\Services\WpImport\Elementor\ChromeFilter;
use App\Services\WpImport\Elementor\ElementorDocument;
use App\Services\WpImport\Support\ContentSanitizer;
use App\Services\WpImport\Support\PromotionalClaims;
use App\Services\WpImport\Support\TitleCleaner;
use Illuminate\Console\OutputStyle;
use Illuminate\Database\Eloquent\Model;

/**
 * Everything the importers share: the source database, the shared services and
 * the switches the command was run with.
 */
class ImportContext
{
    private ?ChromeFilter $chrome = null;

    /** @var array<int, array{id: int, kind: string, titles: array<int, string>}> */
    private array $treatments = [];

    /** @var array<string, int> YouTube id => video record. */
    private array $videos = [];

    public function __construct(
        public readonly WpDatabase $wp,
        public readonly MediaLibrary $media,
        public readonly ImportReport $report,
        public readonly ContentSanitizer $sanitizer,
        public readonly PromotionalClaims $claims,
        public readonly TitleCleaner $titles,
        public readonly OutputStyle $output,
        public readonly bool $dryRun = false,
        public readonly bool $fresh = false,
    ) {}

    public static function make(OutputStyle $output, bool $dryRun = false, bool $fresh = false): self
    {
        $wp = WpDatabase::make();

        return new self(
            $wp,
            MediaLibrary::make($wp, $dryRun),
            new ImportReport,
            ContentSanitizer::fromConfig(),
            PromotionalClaims::fromConfig(),
            TitleCleaner::fromConfig(),
            $output,
            $dryRun,
            $fresh,
        );
    }

    /**
     * The chrome filter, taught on every published page and post so the
     * repetition rule has the whole site to count across.
     */
    public function chrome(): ChromeFilter
    {
        return $this->chrome ??= ChromeFilter::fromConfig()->learn($this->allDocuments());
    }

    /**
     * A block mapper for one locale.
     */
    public function mapper(string $locale): BlockMapper
    {
        return (new BlockMapper(
            new BlockFactory($locale),
            $this->chrome(),
            $this->sanitizer,
            $this->claims,
            $this->titles,
        ))->withTreatments($this->treatments)->withVideos($this->videos);
    }

    /**
     * Records the treatments the page mapper may link its card grids to.
     *
     * @param  array<int, array{id: int, kind: string, titles: array<int, string>}>  $treatments
     */
    public function rememberTreatments(array $treatments): void
    {
        $this->treatments = $treatments;
    }

    /**
     * Records the videos the page mapper may point its players at.
     *
     * @param  array<string, int>  $videos  YouTube id => record id
     */
    public function rememberVideos(array $videos): void
    {
        $this->videos = $videos;
    }

    public function document(int $wpId): ElementorDocument
    {
        return ElementorDocument::fromArray($wpId, $this->wp->elementorData($wpId));
    }

    /**
     * Every published page and post as an Elementor document.
     *
     * @return array<int, ElementorDocument>
     */
    public function allDocuments(): array
    {
        $documents = [];

        foreach (['page', 'post'] as $postType) {
            foreach ($this->wp->published($postType) as $row) {
                $document = $this->document((int) $row->ID);

                if (! $document->isEmpty()) {
                    $documents[] = $document;
                }
            }
        }

        return $documents;
    }

    /**
     * The record a WordPress row was imported into, if the import already ran.
     *
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $model
     * @return TModel|null
     */
    public function mapped(string $wpType, int $wpId, string $model): ?Model
    {
        $map = WpImportMap::query()
            ->where('wp_type', $wpType)
            ->where('wp_id', $wpId)
            ->where('model_type', $model)
            ->first();

        return $map ? $model::query()->find($map->model_id) : null;
    }

    /**
     * Writes down which record a WordPress row became, so a second run updates
     * it instead of creating a duplicate.
     */
    public function remember(string $wpType, int $wpId, Model $model, ?string $locale = null): void
    {
        if ($this->dryRun) {
            return;
        }

        WpImportMap::query()->updateOrCreate(
            ['wp_type' => $wpType, 'wp_id' => $wpId],
            ['model_type' => $model::class, 'model_id' => $model->getKey(), 'locale' => $locale],
        );
    }

    /**
     * Every record of a kind the import created, keyed by WordPress id.
     *
     * @param  class-string<Model>  $model
     * @return array<int, int>
     */
    public function mappedIds(string $wpType, string $model): array
    {
        return WpImportMap::query()
            ->where('wp_type', $wpType)
            ->where('model_type', $model)
            ->pluck('model_id', 'wp_id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }

    public function info(string $message): void
    {
        $this->output->writeln("  <fg=gray>{$message}</>");
    }
}
