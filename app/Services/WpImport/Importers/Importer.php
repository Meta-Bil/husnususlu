<?php

namespace App\Services\WpImport\Importers;

use App\Services\WpImport\ImportContext;

/**
 * One step of `php artisan wp:import`.
 *
 * Every step is idempotent: it looks its records up through `wp_import_maps`
 * and updates what it finds, so the import can be re-run at any time.
 */
abstract class Importer
{
    public function __construct(protected readonly ImportContext $context) {}

    /**
     * The `--only=` name of this step.
     */
    abstract public static function key(): string;

    abstract public function import(): void;

    /**
     * Removes what this step created, for `--fresh`.
     */
    public function fresh(): void {}
}
