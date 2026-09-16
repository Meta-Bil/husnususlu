@php
    /** @var string|null $image */
    /** @var array<int, array{label: string|null, value: string|null}> $facts */
    /** @var array<int, array{label: string, url: string}> $links */
@endphp

<section class="bg-paper">
    <div class="mx-auto grid max-w-[1200px] items-center gap-12 px-6 py-16 lg:grid-cols-[440px_minmax(0,1fr)] lg:gap-24 lg:px-0 lg:py-28">
        @if ($image)
            <div class="relative">
                <img src="{{ $image }}" alt="{{ $name }}" class="h-96 w-full object-cover object-top contrast-[1.04] grayscale lg:h-[600px]" loading="lazy">
                <span class="pointer-events-none absolute -top-5 -end-5 size-30 border-t border-e border-gold-500" aria-hidden="true"></span>
            </div>
        @endif

        <div class="flex flex-col gap-6">
            @if ($eyebrow)
                <p class="text-xs font-semibold tracking-[0.3em] text-gold-700 uppercase">{{ $eyebrow }}</p>
            @endif

            <div class="flex flex-col gap-2">
                <h2 class="font-display text-4xl leading-none font-medium text-navy-900 lg:text-6xl">{{ $name }}</h2>
                <p class="text-base font-semibold tracking-wide text-gold-700">{{ $role }}</p>
            </div>

            @if ($text)
                <p class="max-w-[620px] text-[17px] leading-[1.75] text-ink-soft text-pretty">{{ $text }}</p>
            @endif

            @if (filled($facts))
                <dl class="flex flex-col border-t border-line">
                    @foreach ($facts as $fact)
                        <div class="grid gap-1 border-b border-line py-4 text-[15px] sm:grid-cols-[180px_minmax(0,1fr)] sm:gap-4">
                            <dt class="font-semibold text-gold-700">{{ $fact['label'] }}</dt>
                            <dd class="leading-relaxed text-ink">{{ $fact['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif

            @if (filled($links))
                <div class="flex flex-wrap gap-x-8 gap-y-3">
                    @foreach ($links as $link)
                        <a href="{{ $link['url'] }}" class="inline-flex items-center gap-2.5 text-[14.5px] font-bold text-navy-900 hover:text-gold-700">
                            {{ $link['label'] }}
                            <svg class="size-4 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
