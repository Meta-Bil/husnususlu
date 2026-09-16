@php
    /** @var array<string, mixed> $featured */
    /** @var array<int, array<string, mixed>> $cards */
@endphp

<section class="bg-ivory">
    <div class="mx-auto flex max-w-[1200px] flex-col gap-14 px-6 py-16 lg:px-0 lg:py-28 lg:gap-16">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-end lg:gap-20">
            @include('blocks.partials.section-heading', ['showLead' => false])

            @if (filled($lead))
                <p class="text-[17px] leading-relaxed text-ink-muted text-pretty">{{ $lead }}</p>
            @endif
        </div>

        @if (filled($featured['title']) || filled($featured['image']))
            <div class="grid bg-navy-900 lg:grid-cols-[minmax(0,620px)_minmax(0,1fr)]">
                @if ($featured['image'])
                    <img src="{{ $featured['image'] }}" alt="{{ $featured['title'] }}" class="h-64 w-full object-cover sm:h-96 lg:h-full" loading="lazy">
                @endif

                <div class="flex flex-col justify-center gap-5 p-8 sm:p-12 lg:p-14">
                    @if ($featured['eyebrow'])
                        <p class="text-[11.5px] font-semibold tracking-[0.3em] text-gold-500 uppercase">{{ $featured['eyebrow'] }}</p>
                    @endif

                    @if ($featured['title'])
                        <h3 class="font-display text-4xl leading-none font-medium text-cream-bright lg:text-5xl">{{ $featured['title'] }}</h3>
                    @endif

                    @if ($featured['text'])
                        <p class="text-[16.5px] leading-relaxed text-mist text-pretty">{{ $featured['text'] }}</p>
                    @endif

                    @if (filled($featured['facts']))
                        <dl class="flex flex-wrap gap-x-9 gap-y-5 pt-1">
                            @foreach ($featured['facts'] as $fact)
                                <div class="flex flex-col gap-1">
                                    <dt class="font-display text-3xl text-gold-400">{{ $fact['value'] }}</dt>
                                    <dd class="text-xs tracking-wide text-mist-dim">{{ $fact['label'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    @endif

                    @if ($featured['linkUrl'])
                        <a href="{{ $featured['linkUrl'] }}" class="inline-flex items-center gap-2.5 pt-1 text-sm font-semibold tracking-wide text-gold-300 hover:text-gold-400">
                            {{ $featured['linkLabel'] }}
                            <svg class="size-4 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        @if (filled($cards))
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($cards as $card)
                    @php($tag = $card['url'] ? 'a' : 'div')
                    <{{ $tag }} @if ($card['url']) href="{{ $card['url'] }}" @endif class="flex flex-col gap-4 border border-line bg-paper p-8 transition-colors sm:p-9 hover:border-gold-500">
                        <span class="font-display text-xl text-gold-700 not-italic ltr:italic">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="font-display text-[28px] leading-tight font-semibold text-navy-900 sm:text-[32px]">{{ $card['title'] }}</span>

                        @if ($card['eyebrow'])
                            <span class="text-xs font-semibold tracking-[0.22em] text-gold-700 uppercase">{{ $card['eyebrow'] }}</span>
                        @endif

                        @if ($card['text'])
                            <span class="text-[15.5px] leading-relaxed text-ink-muted">{{ $card['text'] }}</span>
                        @endif

                        @if ($card['url'])
                            <span class="inline-flex items-center gap-2 pt-2 text-sm font-semibold text-navy-900">
                                {{ $card['linkLabel'] }}
                                <svg class="size-4 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </span>
                        @endif
                    </{{ $tag }}>
                @endforeach
            </div>
        @endif
    </div>
</section>
