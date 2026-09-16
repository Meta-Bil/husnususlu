<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\AppointmentRequests\AppointmentRequestResource;
use App\Filament\Resources\Menus\MenuResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\PostCategories\PostCategoryResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Redirects\RedirectResource;
use App\Filament\Resources\Treatments\TreatmentResource;
use App\Filament\Resources\Videos\VideoResource;
use App\Filament\Widgets\AppointmentStatsWidget;
use App\Filament\Widgets\LatestAppointmentsWidget;
use Filament\Pages\Dashboard;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Renders the whole panel shell — navigation, groups and badges included —
 * rather than a single Livewire component in isolation.
 */
class AdminPanelNavigationTest extends AdminPanelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        /*
         * Filament's `Authenticate` middleware only lets a user that does not
         * implement `FilamentUser` into a panel in the local environment, and
         * the test environment is not local. The panel itself is what is under
         * test here, not that guard.
         */
        config(['app.env' => 'local']);
    }

    /**
     * @return array<string, array{0: class-string}>
     */
    public static function resources(): array
    {
        return [
            'pages' => [PageResource::class],
            'treatments' => [TreatmentResource::class],
            'posts' => [PostResource::class],
            'post categories' => [PostCategoryResource::class],
            'videos' => [VideoResource::class],
            'menus' => [MenuResource::class],
            'appointment requests' => [AppointmentRequestResource::class],
            'redirects' => [RedirectResource::class],
        ];
    }

    #[DataProvider('resources')]
    public function test_the_resource_index_renders_inside_the_panel(string $resource): void
    {
        $this->get($resource::getUrl('index'))->assertOk();
    }

    public function test_the_dashboard_renders_with_the_appointment_widgets(): void
    {
        $this->get(Dashboard::getUrl())
            ->assertOk()
            ->assertSeeLivewire(AppointmentStatsWidget::class)
            ->assertSeeLivewire(LatestAppointmentsWidget::class);
    }

    public function test_the_navigation_groups_are_turkish(): void
    {
        $this->assertSame('İçerik', PageResource::getNavigationGroup());
        $this->assertSame('Tedaviler', TreatmentResource::getNavigationGroup());
        $this->assertSame('İçerik', PostResource::getNavigationGroup());
        $this->assertSame('İçerik', PostCategoryResource::getNavigationGroup());
        $this->assertSame('Medya', VideoResource::getNavigationGroup());
        $this->assertSame('Site', MenuResource::getNavigationGroup());
        $this->assertSame('Randevular', AppointmentRequestResource::getNavigationGroup());
        $this->assertSame('Site', RedirectResource::getNavigationGroup());
    }
}
