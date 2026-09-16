@php
    /** @var array{label: string|null, title: string|null, items: array<int, string>, note: string|null} $positive */
    /** @var array{label: string|null, title: string|null, items: array<int, string>, note: string|null} $negative */
@endphp

<section class="bg-paper">
    <div class="mx-auto flex max-w-[1200px] flex-col gap-10 px-6 py-16 lg:gap-12 lg:px-0 lg:py-28">
        @include('blocks.partials.section-heading', ['size' => 'md'])

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="flex flex-col gap-5 border border-line bg-ivory p-8 sm:p-9">
                @if ($positive['label'])
                    <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.3em] text-gold-700 uppercase">
                        <svg class="size-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
                        {{ $positive['label'] }}
                    </p>
                @endif

                @if ($positive['title'])
                    <h3 class="font-display text-[28px] leading-tight font-semibold text-navy-900 sm:text-3xl">{{ $positive['title'] }}</h3>
                @endif

                @if (filled($positive['items']))
                    <ul class="flex flex-col border-t border-line">
                        @foreach ($positive['items'] as $item)
                            <li class="flex items-start gap-3.5 border-b border-line py-4 text-base leading-relaxed text-ink-soft">
                                <svg class="mt-1 size-4.5 shrink-0 text-gold-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($positive['note'])
                    <p class="text-sm leading-relaxed text-ink-muted">{{ $positive['note'] }}</p>
                @endif
            </div>

            <div class="flex flex-col gap-5 bg-navy-900 p-8 sm:p-9">
                @if ($negative['label'])
                    <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.3em] text-gold-500 uppercase">
                        <svg class="size-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6.5 6.5l11 11M17.5 6.5l-11 11"/></svg>
                        {{ $negative['label'] }}
                    </p>
                @endif

                @if ($negative['title'])
                    <h3 class="font-display text-[28px] leading-tight font-semibold text-cream-bright sm:text-3xl">{{ $negative['title'] }}</h3>
                @endif

                @if (filled($negative['items']))
                    <ul class="flex flex-col border-t border-gold-400/30">
                        @foreach ($negative['items'] as $item)
                            <li class="flex items-start gap-3.5 border-b border-gold-400/20 py-4 text-base leading-relaxed text-mist">
                                <svg class="mt-1 size-4.5 shrink-0 text-gold-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6.5 6.5l11 11M17.5 6.5l-11 11"/></svg>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($negative['note'])
                    <p class="text-sm leading-relaxed text-mist-dim">{{ $negative['note'] }}</p>
                @endif
            </div>
        </div>
    </div>
</section>
