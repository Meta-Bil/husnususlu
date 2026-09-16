@php
    /** @var array<int, array{number: string, title: string|null, text: string|null, url: string|null}> $items */
@endphp

@if (filled($items))
    <section class="bg-ivory">
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

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $item)
                    @php($tag = $item['url'] ? 'a' : 'div')
                    <{{ $tag }} @if ($item['url']) href="{{ $item['url'] }}" @endif class="flex flex-col gap-4 border border-line bg-paper p-8 transition-colors sm:p-9 hover:border-gold-500">
                        <span class="font-display text-xl text-gold-700 not-italic ltr:italic">{{ $item['number'] }}</span>
                        <span class="font-display text-[28px] leading-tight font-semibold text-navy-900 sm:text-[32px]">{{ $item['title'] }}</span>

                        @if ($item['text'])
                            <span class="text-[15.5px] leading-relaxed text-ink-muted">{{ $item['text'] }}</span>
                        @endif

                        @if ($item['url'])
                            <span class="inline-flex items-center gap-2 pt-2 text-sm font-semibold text-navy-900">
                                {{ $detailsLabel }}
                                <svg class="size-4 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </span>
                        @endif
                    </{{ $tag }}>
                @endforeach
            </div>
        </div>
    </section>
@endif
