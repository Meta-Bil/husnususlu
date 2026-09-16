<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\AppointmentStatsWidget;
use App\Filament\Widgets\LatestAppointmentsWidget;
use App\Models\AppointmentRequest;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

class AppointmentWidgetsTest extends AdminPanelTestCase
{
    public function test_the_stats_widget_counts_new_weekly_and_monthly_requests(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 17, 12));

        $this->makeRequest(now());
        $this->makeRequest(now()->subDays(2));
        $this->makeRequest(now()->startOfMonth()->addDay());
        $this->makeRequest(now()->subMonths(2));

        $widget = new AppointmentStatsWidget;

        $stats = (fn (): array => $this->getStats())->call($widget);

        $this->assertSame(4, $stats[0]->getValue(), 'Every request is still unhandled.');
        $this->assertSame(2, $stats[1]->getValue(), 'Only this week’s requests are counted.');
        $this->assertSame(3, $stats[2]->getValue(), 'Only this month’s requests are counted.');

        Carbon::setTestNow();
    }

    public function test_the_stats_widget_renders(): void
    {
        $this->makeRequest(now());

        Livewire::test(AppointmentStatsWidget::class)
            ->assertOk()
            ->assertSee('Yeni talepler');
    }

    public function test_the_latest_widget_shows_only_the_five_newest_requests(): void
    {
        $requests = collect(range(1, 6))
            ->map(fn (int $day): AppointmentRequest => $this->makeRequest(now()->subDays($day), "Hasta {$day}"));

        Livewire::test(LatestAppointmentsWidget::class)
            ->assertOk()
            ->assertCanSeeTableRecords($requests->take(5)->all())
            ->assertCanNotSeeTableRecords([$requests->last()]);
    }

    private function makeRequest(Carbon $createdAt, string $name = 'Ayşe Yılmaz'): AppointmentRequest
    {
        $request = AppointmentRequest::create([
            'name' => $name,
            'phone' => '05321234567',
            'complaint' => 'Bel ağrısı',
            'locale' => 'tr',
        ]);

        return $request->forceFill(['created_at' => $createdAt])->save() ? $request->refresh() : $request;
    }
}
