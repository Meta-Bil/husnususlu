@php
    /** @var bool $showWhatsapp */
    /** @var bool $showPhone */
    $settings = app(\App\Settings\SiteSettings::class);
@endphp

<section class="bg-navy-900">
    <div class="mx-auto grid max-w-[1200px] gap-10 px-6 py-16 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end lg:gap-20 lg:px-0 lg:py-28">
        @include('blocks.partials.section-heading', ['tone' => 'dark'])

        <div class="flex flex-col gap-5">
            <div class="flex flex-wrap items-center gap-4">
                <x-ui.button :href="$buttonUrl" variant="gold" icon="arrow">{{ $buttonLabel }}</x-ui.button>

                @if ($showWhatsapp)
                    <x-ui.button
                        :href="'https://wa.me/' . preg_replace('/\D/', '', $settings->whatsapp_phone)"
                        variant="outline"
                        icon="whatsapp"
                        target="_blank"
                    >{{ __('front.write_on_whatsapp') }}</x-ui.button>
                @endif
            </div>

            @if ($showPhone)
                <p class="text-sm text-mist-dim">
                    {{ __('front.appointment_by_phone') }}
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $settings->whatsapp_phone) }}" class="font-semibold text-cream hover:text-gold-300">{{ $settings->whatsapp_phone }}</a>
                </p>
            @endif
        </div>
    </div>
</section>
