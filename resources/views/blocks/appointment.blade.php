@php
    /** @var bool $showContactLines */
    /** @var bool $calendarEnabled */
@endphp

<section id="randevu" class="scroll-mt-24 bg-navy-900">
    <div class="mx-auto flex max-w-[1200px] flex-col gap-12 px-6 py-16 lg:px-0 lg:py-28">
        <div class="grid gap-12 lg:grid-cols-[minmax(0,1fr)_600px] lg:gap-24">
            <div class="flex flex-col gap-7">
                @include('blocks.partials.section-heading', ['tone' => 'dark'])

                @if ($showContactLines)
                    <ul class="flex flex-col gap-4 pt-2 text-[15.5px] text-cream">
                        @if ($clinicPhone || $whatsappPhone)
                            <li class="flex items-start gap-3.5">
                                <svg class="mt-0.5 size-5 shrink-0 text-gold-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>
                                <span class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                                    @if ($clinicPhone)
                                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $clinicPhone) }}" class="hover:text-gold-300">{{ $clinicPhone }}</a>
                                    @endif
                                    @if ($clinicPhone && $whatsappPhone)
                                        <span class="text-mist-dim" aria-hidden="true">·</span>
                                    @endif
                                    @if ($whatsappPhone)
                                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $whatsappPhone) }}" class="hover:text-gold-300">{{ $whatsappPhone }}</a>
                                    @endif
                                </span>
                            </li>
                        @endif

                        @if ($email)
                            <li class="flex items-start gap-3.5">
                                <svg class="mt-0.5 size-5 shrink-0 text-gold-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                                <a href="mailto:{{ $email }}" class="hover:text-gold-300">{{ $email }}</a>
                            </li>
                        @endif

                        @if ($address)
                            <li class="flex items-start gap-3.5">
                                <svg class="mt-0.5 size-5 shrink-0 text-gold-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
                                <span class="leading-relaxed">{{ $address }}</span>
                            </li>
                        @endif
                    </ul>
                @endif
            </div>

            <div class="min-w-0">
                <livewire:appointment-form />
            </div>
        </div>

        @if ($calendarEnabled)
            <div class="flex flex-col gap-6 border border-gold-400/25 bg-navy-800 p-8 sm:p-10">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <div class="flex flex-col gap-3">
                        <p class="text-xs font-semibold tracking-[0.3em] text-gold-500 uppercase">DoktorTakvimi</p>
                        <h3 class="font-display text-3xl leading-none font-medium text-cream-bright lg:text-[40px]">{{ __('front.online_calendar') }}</h3>
                    </div>

                    @if ($calendarUrl)
                        <a href="{{ $calendarUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2.5 text-sm font-semibold text-gold-300 hover:text-gold-400">
                            {{ __('front.appointment') }}
                            <svg class="size-4 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                    @endif
                </div>

                <div class="flex flex-col items-center gap-4 border border-gold-400/20 p-8 text-center sm:p-10">
                    <svg class="size-8 text-gold-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4.5" width="18" height="16" rx="1.5"/><path d="M3 9.5h18M8 2.5v4M16 2.5v4M7.5 13.5h2M11 13.5h2M14.5 13.5h2M7.5 17h2M11 17h2"/></svg>

                    @if ($calendarText)
                        <p class="max-w-lg text-[15.5px] leading-relaxed text-mist">{{ $calendarText }}</p>
                    @endif

                    @if ($calendarUrl)
                        <div id="doktortakvimi-widget" data-url="{{ $calendarUrl }}" class="w-full"></div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>
