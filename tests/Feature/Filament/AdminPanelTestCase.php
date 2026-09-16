<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PDO;
use Tests\TestCase;

/**
 * Shared setup for the admin panel tests: a logged in user, the admin panel as
 * the current one, and a database the schema can actually be created in.
 *
 * The content tables carry generated `slug_{locale}` columns built with MySQL's
 * `json_unquote()`, which SQLite has no equivalent for, so the in-memory
 * connection configured in `phpunit.xml` cannot run the migrations. These tests
 * therefore use a dedicated MySQL database, created on first use and rebuilt by
 * `RefreshDatabase`; the application's own database is never touched.
 */
abstract class AdminPanelTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    public function createApplication(): Application
    {
        $app = parent::createApplication();

        $connection = $app['config']->get('database.connections.mysql');
        $connection['url'] = null;
        $connection['database'] = env('DB_TEST_DATABASE', 'husnususlu1_test');

        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', $connection);

        static::ensureTestDatabaseExists($connection);

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $this->actingAs($this->admin);

        Filament::setCurrentPanel('admin');
    }

    /**
     * @param  array<string, mixed>  $connection
     */
    private static function ensureTestDatabaseExists(array $connection): void
    {
        static $created = false;

        if ($created) {
            return;
        }

        $created = true;

        $pdo = new PDO(
            "mysql:host={$connection['host']};port={$connection['port']}",
            $connection['username'],
            $connection['password'],
        );

        $pdo->exec("create database if not exists `{$connection['database']}` character set utf8mb4 collate utf8mb4_unicode_ci");
    }
}
