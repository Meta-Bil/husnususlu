<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Fills a fresh database from the old WordPress site.
 *
 * The import itself lives in `php artisan wp:import`; this only makes it part
 * of the normal setup, so `migrate:fresh --seed` gives a working site. It is
 * skipped silently when the old database is not configured, which is the case
 * on any machine that does not have a copy of it.
 */
class WpImportSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (blank(config('database.connections.wp.database'))) {
            $this->command?->warn('Eski WordPress veritabanı tanımlı değil; içe aktarma atlandı.');

            return;
        }

        $this->command?->call('wp:import', ['--no-interaction' => true]);
    }
}
