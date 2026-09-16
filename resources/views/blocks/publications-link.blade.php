@php
    /** @var string|null $linkUrl */
@endphp

@if ($linkUrl)
    <section class="bg-navy-900">
        <div class="mx-auto flex max-w-[1200px] flex-col gap-6 border-t border-gold-400/20 px-6 py-10 sm:flex-row sm:items-center sm:justify-between sm:gap-12 lg:px-0 lg:py-12">
            <div class="flex items-start gap-4">
                <svg class="mt-1 size-5.5 shrink-0 text-gold-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5.5A1.5 1.5 0 0 1 5.5 4H11v16H5.5A1.5 1.5 0 0 1 4 18.5z"/><path d="M20 5.5A1.5 1.5 0 0 0 18.5 4H13v16h5.5a1.5 1.5 0 0 0 1.5-1.5z"/></svg>

                <div class="flex flex-col gap-2">
                    @if ($eyebrow)
                        <p class="text-[11.5px] font-semibold tracking-[0.3em] text-gold-500 uppercase">{{ $eyebrow }}</p>
                    @endif

                    @if ($title)
                        <p class="font-display text-2xl leading-snug text-cream-bright sm:text-[28px]">{{ $title }}</p>
                    @endif

                    @if ($lead)
                        <p class="max-w-xl text-[14.5px] leading-relaxed text-mist-muted">{{ $lead }}</p>
                    @endif
                </div>
            </div>

            <a href="{{ $linkUrl }}" target="_blank" rel="noopener" class="inline-flex shrink-0 items-center gap-2.5 text-[14.5px] font-semibold text-gold-300 hover:text-gold-400">
                {{ $linkLabel }}
                <svg class="size-4 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </div>
    </section>
@endif
