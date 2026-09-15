@php
    /** @var string|null $eyebrow */
    /** @var string|null $title */
    /** @var string|null $accent */
    /** @var string|null $lead */
    /** @var string|null $image */
    /** @var array<int, array{value: string|null, label: string|null}> $facts */
    $settings = app(\App\Settings\SiteSettings::class);
    $isHome = ($variant ?? 'page') === 'home';
@endphp

<section class="bg-navy-900 text-cream-bright">
    <div class="mx-auto flex max-w-[1200px] flex-col gap-12 px-6 py-14 lg:flex-row lg:items-center lg:gap-20 lg:px-0 lg:py-20">
        <div class="flex flex-col gap-7 lg:flex-1">
            @if ($eyebrow)
                <p class="flex items-center gap-4 text-xs font-semibold tracking-[0.3em] text-gold-500 uppercase">
                    <span class="hidden h-px w-12 bg-gold-500 sm:block"></span>{{ $eyebrow }}
                </p>
            @endif

            @if ($title)
                <h1 @class([
                    'font-display font-medium leading-[0.95] text-cream-bright',
                    'text-5xl sm:text-6xl lg:text-8xl' => $isHome,
                    'text-4xl sm:text-5xl lg:text-7xl' => ! $isHome,
                ])>
                    {!! nl2br(e($title)) !!}
                    @if ($accent)
                        <em class="block font-medium text-gold-400 not-italic rtl:not-italic ltr:italic">{{ $accent }}</em>
                    @endif
                </h1>
            @endif

            @if ($lead)
                <p class="max-w-xl text-lg leading-relaxed text-mist text-pretty">{{ $lead }}</p>
            @endif

            @if ($showCta ?? true)
                <div class="flex flex-wrap items-center gap-4">
                    <x-ui.button :href="'#randevu'" variant="gold">{{ __('front.request_appointment') }}</x-ui.button>
                    <x-ui.button :href="'https://wa.me/' . preg_replace('/\D/', '', $settings->whatsapp_phone)" variant="outline" icon="whatsapp" target="_blank">
                        {{ __('front.write_on_whatsapp') }}
                    </x-ui.button>
                </div>
            @endif

            @if ($showPhone ?? true)
                <p class="text-sm text-mist-dim">
                    {{ __('front.appointment_by_phone') }}
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $settings->whatsapp_phone) }}" class="font-semibold text-cream">{{ $settings->whatsapp_phone }}</a>
                </p>
            @endif
        </div>

        @if ($image && ($variant ?? 'page') !== 'facts')
            <div class="relative lg:w-[42%] lg:shrink-0">
                <div class="pointer-events-none absolute inset-y-6 start-6 -end-6 border border-gold-500/55" aria-hidden="true"></div>
                <img src="{{ $image }}" alt="{{ $title }}" class="relative h-[320px] w-full object-cover sm:h-[460px] lg:h-[620px]" loading="eager">
            </div>
        @endif

        @if (($variant ?? 'page') === 'facts' && filled($facts))
            <div class="border border-gold-500/30 bg-navy-800 p-8 lg:w-[38%] lg:shrink-0">
                <p class="text-xs font-semibold tracking-[0.3em] text-gold-500 uppercase">{{ __('front.at_a_glance') }}</p>
                <dl class="mt-6 grid grid-cols-2 gap-x-8 gap-y-6">
                    @foreach ($facts as $fact)
                        <div class="flex flex-col gap-1">
                            <dt class="font-display text-3xl text-gold-400">{{ $fact['value'] }}</dt>
                            <dd class="text-xs text-mist-dim">{{ $fact['label'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        @endif
    </div>
</section>
