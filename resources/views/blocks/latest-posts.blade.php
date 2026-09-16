@php
    /** @var array<int, array{title: string|null, excerpt: string|null, category: string|null, date: string|null, url: string|null}> $items */
@endphp

@if (filled($items))
    <section class="bg-paper">
        <div class="mx-auto flex max-w-[1200px] flex-col gap-10 px-6 py-16 lg:gap-12 lg:px-0 lg:py-28">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between sm:gap-10">
                @include('blocks.partials.section-heading', ['size' => 'md', 'lead' => null])

                @if ($linkUrl && $linkLabel)
                    <a href="{{ $linkUrl }}" class="inline-flex shrink-0 items-center gap-2.5 text-[14.5px] font-bold text-navy-900 hover:text-gold-700">
                        {{ $linkLabel }}
                        <svg class="size-4 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                @endif
            </div>

            <ul class="grid gap-10 md:grid-cols-2 lg:grid-cols-3 lg:gap-12">
                @foreach ($items as $item)
                    <li class="border-t-2 border-navy-900 pt-6">
                        @php($tag = $item['url'] ? 'a' : 'div')
                        <{{ $tag }} @if ($item['url']) href="{{ $item['url'] }}" @endif class="group flex flex-col gap-4">
                            @if ($item['category'] || $item['date'])
                                <span class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1 text-xs font-semibold tracking-[0.16em] text-gold-700 uppercase">
                                    <span>{{ $item['category'] }}</span>
                                    <span class="text-mist-dim">{{ $item['date'] }}</span>
                                </span>
                            @endif

                            <span class="font-display text-[28px] leading-tight font-semibold text-navy-900 transition-colors group-hover:text-gold-700 sm:text-[32px]">{{ $item['title'] }}</span>

                            @if ($item['excerpt'])
                                <span class="text-[15px] leading-relaxed text-ink-muted">{{ $item['excerpt'] }}</span>
                            @endif
                        </{{ $tag }}>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
