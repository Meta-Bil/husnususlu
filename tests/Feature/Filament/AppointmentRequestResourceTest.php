<?php

namespace Tests\Feature\Filament;

use App\Enums\AppointmentStatus;
use App\Filament\Resources\AppointmentRequests\AppointmentRequestResource;
use App\Filament\Resources\AppointmentRequests\Pages\EditAppointmentRequest;
use App\Filament\Resources\AppointmentRequests\Pages\ListAppointmentRequests;
use App\Models\AppointmentRequest;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

class AppointmentRequestResourceTest extends AdminPanelTestCase
{
    public function test_it_lists_requests_newest_first(): void
    {
        $older = $this->makeRequest('Ayşe Yılmaz', now()->subDays(3));
        $newer = $this->makeRequest('Mehmet Demir', now());

        Livewire::test(ListAppointmentRequests::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$newer, $older], inOrder: true);
    }

    public function test_it_filters_by_status(): void
    {
        $new = $this->makeRequest();
        $closed = $this->makeRequest('Kapanmış Talep');
        $closed->forceFill(['status' => AppointmentStatus::Closed])->save();

        Livewire::test(ListAppointmentRequests::class)
            ->filterTable('status', AppointmentStatus::Closed->value)
            ->assertCanSeeTableRecords([$closed])
            ->assertCanNotSeeTableRecords([$new]);
    }

    public function test_it_filters_by_arrival_date(): void
    {
        $old = $this->makeRequest('Eski Talep', now()->subMonth());
        $recent = $this->makeRequest('Yeni Talep', now()->subDay());

        Livewire::test(ListAppointmentRequests::class)
            ->filterTable('created_at', ['from' => now()->subWeek()->toDateString()])
            ->assertCanSeeTableRecords([$recent])
            ->assertCanNotSeeTableRecords([$old]);
    }

    public function test_requests_cannot_be_created_from_the_panel(): void
    {
        $this->assertFalse(AppointmentRequestResource::canCreate());
        $this->assertArrayNotHasKey('create', AppointmentRequestResource::getPages());
    }

    public function test_the_navigation_badge_counts_unhandled_requests(): void
    {
        $this->assertNull(AppointmentRequestResource::getNavigationBadge());

        $this->makeRequest();
        $this->makeRequest('İkinci Talep');

        $this->assertSame('2', AppointmentRequestResource::getNavigationBadge());
    }

    public function test_it_saves_the_status_and_notes(): void
    {
        $request = $this->makeRequest();

        Livewire::test(EditAppointmentRequest::class, ['record' => $request->getKey()])
            ->assertOk()
            ->fillForm([
                'status' => AppointmentStatus::Scheduled->value,
                'admin_notes' => 'Salı 14:00 için randevu verildi.',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $request->refresh();

        $this->assertSame(AppointmentStatus::Scheduled, $request->status);
        $this->assertSame('Salı 14:00 için randevu verildi.', $request->admin_notes);
    }

    private function makeRequest(string $name = 'Ayşe Yılmaz', ?Carbon $createdAt = null): AppointmentRequest
    {
        $request = AppointmentRequest::create([
            'name' => $name,
            'phone' => '05321234567',
            'complaint' => 'Bel ağrısı',
            'preferred_time' => 'Öğleden sonra',
            'preferred_date' => now()->addWeek()->toDateString(),
            'locale' => 'tr',
        ]);

        if ($createdAt) {
            $request->forceFill(['created_at' => $createdAt])->save();
        }

        return $request;
    }
}
