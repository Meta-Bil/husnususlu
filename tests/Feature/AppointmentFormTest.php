<?php

namespace Tests\Feature;

use App\Models\AppointmentRequest;
use App\Notifications\AppointmentRequestReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class AppointmentFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_visitor_can_send_an_appointment_request(): void
    {
        Notification::fake();

        Livewire::test('appointment-form')
            ->set('name', 'Ayşe Yılmaz')
            ->set('phone', '+90 555 111 22 33')
            ->set('complaint', 'Bel fıtığı')
            ->set('preferred_time', 'morning')
            ->set('consent', true)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        $request = AppointmentRequest::sole();

        $this->assertSame('Ayşe Yılmaz', $request->name);
        $this->assertSame('tr', $request->locale);
        $this->assertNotNull($request->consented_at);

        Notification::assertSentOnDemand(AppointmentRequestReceived::class);
    }

    public function test_the_form_requires_a_name_a_phone_and_consent(): void
    {
        Livewire::test('appointment-form')
            ->call('submit')
            ->assertHasErrors(['name', 'phone', 'consent']);

        $this->assertSame(0, AppointmentRequest::count());
    }

    public function test_a_filled_honeypot_is_silently_discarded(): void
    {
        Notification::fake();

        Livewire::test('appointment-form')
            ->set('name', 'Bot')
            ->set('phone', '+90 555 000 00 00')
            ->set('consent', true)
            ->set('website', 'https://spam.example')
            ->call('submit')
            ->assertSet('sent', true);

        $this->assertSame(0, AppointmentRequest::count());
        Notification::assertNothingSent();
    }

    public function test_a_past_date_is_rejected(): void
    {
        Livewire::test('appointment-form')
            ->set('name', 'Ayşe Yılmaz')
            ->set('phone', '+90 555 111 22 33')
            ->set('preferred_date', now()->subWeek()->toDateString())
            ->set('consent', true)
            ->call('submit')
            ->assertHasErrors('preferred_date');
    }
}
