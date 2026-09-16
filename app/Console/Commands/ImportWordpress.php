<?php

namespace App\Console\Commands;

use App\Services\WpImport\ImportContext;
use App\Services\WpImport\Importers\Importer;
use App\Services\WpImport\Importers\MediaImporter;
use App\Services\WpImport\Importers\MenuImporter;
use App\Services\WpImport\Importers\PageImporter;
use App\Services\WpImport\Importers\PostImporter;
use App\Services\WpImport\Importers\RedirectImporter;
use App\Services\WpImport\Importers\SeoImporter;
use App\Services\WpImport\Importers\TreatmentImporter;
use App\Services\WpImport\Importers\VideoImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

/**
 * Brings the old WordPress site across.
 *
 * The source database is read-only and the import is idempotent: every record
 * is looked up through `wp_import_maps` and updated, so the command can be run
 * again after a content fix without creating duplicates.
 */
#[Signature('wp:import
    {--only= : Comma separated steps to run: media,treatments,pages,posts,videos,menus,seo,redirects}
    {--dry-run : Report what would happen without writing anything}
    {--fresh : Delete what a previous import created before running}')]
#[Description('Imports the old WordPress site into this application')]
class ImportWordpress extends Command
{
    /**
     * In dependency order: media builds the attachment index, videos and
     * treatments must exist before pages can link their players and card
     * grids to them, posts before menus can point at them, and redirects last
     * of all because they need every record's new URL.
     *
     * @var array<int, class-string<Importer>>
     */
    private const STEPS = [
        MediaImporter::class,
        VideoImporter::class,
        TreatmentImporter::class,
        PageImporter::class,
        PostImporter::class,
        MenuImporter::class,
        SeoImporter::class,
        RedirectImporter::class,
    ];

    public function handle(): int
    {
        $steps = $this->selectedSteps();

        if ($steps === null) {
            return self::FAILURE;
        }

        $context = ImportContext::make($this->output, (bool) $this->option('dry-run'), (bool) $this->option('fresh'));

        if (! $context->wp->isReachable()) {
            $this->components->error('Eski WordPress veritabanına bağlanılamadı. DB_WP_* ayarlarını kontrol edin.');

            return self::FAILURE;
        }

        if ($context->dryRun) {
            $this->components->warn('Deneme çalışması: hiçbir kayıt yazılmayacak.');
        }

        if ($context->fresh && ! $this->confirmFresh()) {
            return self::FAILURE;
        }

        $importers = array_map(fn (string $step): Importer => new $step($context), $steps);

        if ($context->fresh) {
            foreach (array_reverse($importers) as $importer) {
                $importer->fresh();
            }
        }

        foreach ($importers as $importer) {
            if (! $this->runStep($importer)) {
                return self::FAILURE;
            }
        }

        $this->summarise($context);

        return self::SUCCESS;
    }

    /**
     * @return array<int, class-string<Importer>>|null
     */
    private function selectedSteps(): ?array
    {
        $only = (string) ($this->option('only') ?? '');

        if (trim($only) === '') {
            return self::STEPS;
        }

        $wanted = array_filter(array_map(trim(...), explode(',', $only)));
        $available = [];

        foreach (self::STEPS as $step) {
            $available[$step::key()] = $step;
        }

        if ($unknown = array_diff($wanted, array_keys($available))) {
            $this->components->error('Bilinmeyen adım: '.implode(', ', $unknown).'. Seçenekler: '.implode(', ', array_keys($available)));

            return null;
        }

        /* Keep the declared order whatever order the option listed them in. */
        return array_values(array_filter(self::STEPS, fn (string $step): bool => in_array($step::key(), $wanted, true)));
    }

    private function confirmFresh(): bool
    {
        if ($this->option('dry-run') || ! $this->input->isInteractive()) {
            return true;
        }

        return $this->components->confirm('--fresh önceki içe aktarmanın oluşturduğu kayıtları siler. Devam edilsin mi?', false);
    }

    /**
     * Runs one step. A step that throws stops the run rather than leaving half
     * a site behind, and says which step failed.
     */
    private function runStep(Importer $importer): bool
    {
        $failure = null;

        $this->components->task($importer::key(), function () use ($importer, &$failure): bool {
            try {
                $importer->import();

                return true;
            } catch (Throwable $exception) {
                $failure = $exception;

                return false;
            }
        });

        if ($failure === null) {
            return true;
        }

        $this->components->error("`{$importer::key()}` adımı başarısız oldu: {$failure->getMessage()}");
        report($failure);

        return false;
    }

    private function summarise(ImportContext $context): void
    {
        $report = $context->report;

        /* Counted at the end: the content steps attach their own covers, so a
           file may be copied long after the media step has finished. */
        $report->created('media', $context->media->copied());

        $this->newLine();
        $this->table(
            ['Kayıt', 'Yeni', 'Güncellenen', 'Atlanan', 'Toplam'],
            $report->rows(),
        );

        foreach ($report->notes() as $note) {
            $this->components->bulletList(["<fg=yellow>{$note['step']}</>: {$note['note']}"]);
        }

        foreach ($report->warnings() as $warning) {
            $this->components->warn("{$warning['step']}: {$warning['note']}");
        }

        if ($context->dryRun) {
            $this->components->info('Deneme çalışması bitti; veritabanı değişmedi.');

            return;
        }

        $this->components->info('İçe aktarma tamamlandı.');
    }
}
