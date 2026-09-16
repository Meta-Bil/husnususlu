@php
    /** @var array<int, array{number: string, eyebrow: string|null, title: string|null, features: array<int, string>, buttonLabel: string, buttonUrl: string}> $items */
@endphp

@if (filled($items))
    <section class="bg-ivory">
        <div class="mx-auto flex max-w-[1200px] flex-col gap-12 px-6 py-16 lg:gap-16 lg:px-0 lg:py-28">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-end lg:gap-20">
                @include('blocks.partials.section-heading', ['showLead' => false])

                @if (filled($lead))
                    <p class="text-[17px] leading-relaxed text-ink-muted text-pretty">{{ $lead }}</p>
                @endif
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $item)
                    <article class="flex flex-col justify-between gap-9 border border-line bg-paper p-8 sm:p-9">
                        <div class="flex flex-col gap-4">
                            <span class="font-display text-xl text-gold-700 not-italic ltr:italic">{{ $item['number'] }}</span>

                            @if ($item['eyebrow'])
                                <span class="text-xs font-semibold tracking-[0.22em] text-gold-700 uppercase">{{ $item['eyebrow'] }}</span>
                            @endif

                            <h3 class="font-display text-[28px] leading-tight font-semibold text-navy-900 sm:text-[32px]">{{ $item['title'] }}</h3>

                            @if (filled($item['features']))
                                <ul class="flex flex-col border-t border-line">
                                    @foreach ($item['features'] as $feature)
                                        <li class="flex items-center gap-3 border-b border-line py-3.5 text-[15.5px] text-ink">
                                            <svg class="size-4 shrink-0 text-gold-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <a href="{{ $item['buttonUrl'] }}" target="_blank" rel="noopener" class="inline-flex h-13 items-center justify-center gap-2.5 border border-navy-900 px-5 text-[14.5px] font-bold text-navy-900 transition-colors hover:bg-navy-900 hover:text-cream-bright">
                            <svg class="size-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.3 7.4L3 21l2.1-5.6A8.5 8.5 0 1 1 21 11.5z"/></svg>
                            {{ $item['buttonLabel'] }}
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
