@php
    /** @var array<int, array{question: string|null, answer: string|null}> $items */
@endphp

@if (filled($items))
    <section class="bg-paper">
        <div class="mx-auto grid max-w-[1200px] gap-12 px-6 py-16 lg:grid-cols-[360px_minmax(0,1fr)] lg:gap-24 lg:px-0 lg:py-28">
            <div class="flex flex-col gap-5">
                @include('blocks.partials.section-heading', ['size' => 'md'])

                @if ($linkUrl && $linkLabel)
                    <a href="{{ $linkUrl }}" class="inline-flex items-center gap-2.5 pt-2 text-sm font-bold text-navy-900 hover:text-gold-700">
                        {{ $linkLabel }}
                        <svg class="size-4 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                @endif
            </div>

            <div class="flex flex-col border-t border-line">
                @foreach ($items as $item)
                    <details class="group border-b border-line" @if ($loop->first) open @endif>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-8 py-6 text-[17px] leading-snug font-semibold text-navy-900 [&::-webkit-details-marker]:hidden sm:text-lg">
                            {{ $item['question'] }}
                            <span class="grid size-5.5 shrink-0 place-items-center text-gold-700" aria-hidden="true">
                                <span class="col-start-1 row-start-1 h-px w-full bg-current"></span>
                                <span class="col-start-1 row-start-1 h-full w-px bg-current transition-transform group-open:scale-y-0"></span>
                            </span>
                        </summary>

                        @if ($item['answer'])
                            <div class="max-w-[660px] pb-6 text-base leading-relaxed text-ink-muted">
                                {{ $item['answer'] }}
                            </div>
                        @endif
                    </details>
                @endforeach
            </div>
        </div>
    </section>
@endif
