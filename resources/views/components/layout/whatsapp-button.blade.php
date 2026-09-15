@php
    $settings = app(\App\Settings\SiteSettings::class);
    $number = preg_replace('/\D/', '', $settings->whatsapp_phone);
@endphp

@if ($number)
    <a href="https://wa.me/{{ $number }}"
       target="_blank"
       rel="noopener"
       class="fixed bottom-6 end-6 z-40 flex size-15 items-center justify-center rounded-full bg-gold-500 text-navy-900 shadow-[0_10px_30px_rgba(0,0,0,0.35)] transition hover:bg-gold-400"
       aria-label="{{ __('front.write_on_whatsapp') }}">
        <svg class="size-6.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 11.5a8.4 8.4 0 0 1-12.3 7.4L3 21l2.1-5.6A8.5 8.5 0 1 1 21 11.5z"/>
        </svg>
    </a>
@endif
