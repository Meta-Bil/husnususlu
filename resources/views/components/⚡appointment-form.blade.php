<?php

use App\Models\AppointmentRequest;
use App\Notifications\AppointmentRequestReceived;
use App\Settings\SiteSettings;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public string $complaint = '';

    public string $preferred_time = '';

    public string $preferred_date = '';

    public string $message = '';

    public bool $consent = false;

    /** Bots fill hidden fields; people do not. */
    public string $website = '';

    public bool $sent = false;

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'phone' => ['required', 'string', 'min:7', 'max:32'],
            'email' => ['nullable', 'email', 'max:190'],
            'complaint' => ['nullable', 'string', 'max:120'],
            'preferred_time' => ['nullable', 'in:morning,noon,afternoon'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'message' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
        ];
    }

    public function submit(): void
    {
        $this->validate();

        if (filled($this->website)) {
            $this->sent = true;

            return;
        }

        $key = 'appointment-request:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('phone', __('front.form.error'));

            return;
        }

        RateLimiter::hit($key, 3600);

        $appointment = AppointmentRequest::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email ?: null,
            'complaint' => $this->complaint ?: null,
            'preferred_time' => $this->preferred_time ?: null,
            'preferred_date' => $this->preferred_date ?: null,
            'message' => $this->message ?: null,
            'locale' => app()->getLocale(),
            'source_url' => url()->previous(),
            'ip' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
            'consented_at' => now(),
        ]);

        $recipient = app(SiteSettings::class)->appointment_notification_email;

        if (filled($recipient)) {
            Notification::route('mail', $recipient)->notify(new AppointmentRequestReceived($appointment));
            $appointment->forceFill(['notified_at' => now()])->save();
        }

        $this->sent = true;
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function timeOptions(): array
    {
        return [
            ['value' => 'morning', 'label' => __('front.form.morning')],
            ['value' => 'noon', 'label' => __('front.form.noon')],
            ['value' => 'afternoon', 'label' => __('front.form.afternoon')],
        ];
    }
};
?>

<div id="randevu" class="rounded-[28px] bg-paper p-8 shadow-[0_24px_60px_rgba(0,0,0,0.35)] sm:p-9">
    @if ($sent)
        <div class="flex flex-col items-center gap-4 py-12 text-center">
            <span class="flex size-14 items-center justify-center rounded-full bg-gold-500 text-navy-900">
                <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7"/></svg>
            </span>
            <p class="font-display text-3xl text-navy-900">{{ __('front.form.success') }}</p>
        </div>
    @else
        <form wire:submit="submit" class="flex flex-col gap-4">
            <div class="grid gap-3 sm:grid-cols-2">
                <div class="flex flex-col gap-1">
                    <input type="text" wire:model="name" placeholder="{{ __('front.form.name') }}" autocomplete="name"
                           class="h-13 w-full rounded-[14px] border border-line bg-white px-4 text-[15px] text-ink placeholder:text-ink-muted/70 focus:border-gold-500 focus:outline-none">
                    @error('name') <span class="text-xs text-red-700">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col gap-1">
                    <input type="tel" wire:model="phone" placeholder="{{ __('front.form.phone') }}" autocomplete="tel"
                           class="h-13 w-full rounded-[14px] border border-line bg-white px-4 text-[15px] text-ink placeholder:text-ink-muted/70 focus:border-gold-500 focus:outline-none">
                    @error('phone') <span class="text-xs text-red-700">{{ $message }}</span> @enderror
                </div>
            </div>

            <input type="text" wire:model="complaint" placeholder="{{ __('front.form.complaint') }}"
                   class="h-13 w-full rounded-[14px] border border-line bg-white px-4 text-[15px] text-ink placeholder:text-ink-muted/70 focus:border-gold-500 focus:outline-none">

            <div class="flex flex-col gap-2">
                <span class="text-[13.5px] font-semibold text-ink-muted">{{ __('front.form.preferred_time') }}</span>
                <div class="grid grid-cols-3 gap-2">
                    @foreach ($this->timeOptions() as $option)
                        <button type="button" wire:click="$set('preferred_time', '{{ $option['value'] }}')"
                                @class([
                                    'h-12 rounded-xl text-[14.5px] transition',
                                    'bg-navy-900 font-semibold text-cream-bright' => $preferred_time === $option['value'],
                                    'border border-line bg-white text-ink hover:border-gold-500' => $preferred_time !== $option['value'],
                                ])>
                            {{ $option['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <input type="date" wire:model="preferred_date" aria-label="{{ __('front.form.preferred_date') }}"
                   class="h-13 w-full rounded-[14px] border border-line bg-white px-4 text-[15px] text-ink focus:border-gold-500 focus:outline-none">
            @error('preferred_date') <span class="text-xs text-red-700">{{ $message }}</span> @enderror

            <textarea wire:model="message" rows="3" placeholder="{{ __('front.form.message') }}"
                      class="w-full rounded-[14px] border border-line bg-white p-4 text-[15px] text-ink placeholder:text-ink-muted/70 focus:border-gold-500 focus:outline-none"></textarea>

            <div class="hidden" aria-hidden="true">
                <label>Website<input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
            </div>

            <label class="flex items-start gap-3 text-[13px] leading-snug text-ink-muted">
                <input type="checkbox" wire:model="consent" class="mt-0.5 size-[18px] shrink-0 rounded-[5px] border border-navy-900 accent-navy-900">
                <span>{{ __('front.form.consent') }}</span>
            </label>
            @error('consent') <span class="text-xs text-red-700">{{ $message }}</span> @enderror

            <button type="submit" wire:loading.attr="disabled"
                    class="mt-2 flex h-14 items-center justify-center rounded-full bg-gold-500 text-base font-bold text-navy-900 transition hover:bg-gold-400 disabled:opacity-60">
                <span wire:loading.remove wire:target="submit">{{ __('front.form.submit') }}</span>
                <span wire:loading wire:target="submit">…</span>
            </button>
        </form>
    @endif
</div>
