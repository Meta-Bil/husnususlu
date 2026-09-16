<?php

namespace App\Notifications;

use App\Models\AppointmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentRequestReceived extends Notification
{
    use Queueable;

    public function __construct(public AppointmentRequest $appointmentRequest) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $request = $this->appointmentRequest;

        $times = [
            'morning' => 'Sabah',
            'noon' => 'Öğle',
            'afternoon' => 'Öğleden sonra',
        ];

        $mail = (new MailMessage)
            ->subject('Yeni randevu talebi: '.$request->name)
            ->greeting('Yeni bir randevu talebi geldi.')
            ->line('**Ad Soyad:** '.$request->name)
            ->line('**Telefon:** '.$request->phone);

        if ($request->email) {
            $mail->line('**E-posta:** '.$request->email);
        }

        if ($request->complaint) {
            $mail->line('**Şikâyet:** '.$request->complaint);
        }

        if ($request->preferred_date || $request->preferred_time) {
            $mail->line('**Tercih:** '.trim(
                ($request->preferred_date?->translatedFormat('j F Y') ?? '').' '.
                ($times[$request->preferred_time] ?? '')
            ));
        }

        if ($request->message) {
            $mail->line('**Mesaj:** '.$request->message);
        }

        return $mail
            ->line('**Dil:** '.strtoupper($request->locale))
            ->action('Panelde aç', url('/admin/appointment-requests/'.$request->getKey().'/edit'))
            ->salutation('Prof. Dr. Hüsnü Süslü web sitesi');
    }
}
