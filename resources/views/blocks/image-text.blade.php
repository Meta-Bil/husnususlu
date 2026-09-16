@php
    /** @var string|null $image */
    /** @var string|null $body */
    $isDark = ($background ?? 'paper') === 'navy';

    $surface = match ($background ?? 'paper') {
        'ivory' => 'bg-ivory',
        'navy' => 'bg-navy-900',
        default => 'bg-paper',
    };

    $prose = collect([
        'flex flex-col gap-4',
        '[&_p]:text-[17px] [&_p]:leading-[1.8] [&_p]:text-pretty',
        '[&_h3]:text-xl [&_h3]:font-bold [&_h3]:pt-2',
        '[&_ul]:flex [&_ul]:flex-col [&_ul]:gap-2.5',
        "[&_li]:relative [&_li]:ps-6 [&_li]:leading-[1.7] [&_li]:before:absolute [&_li]:before:start-0 [&_li]:before:top-[0.65em] [&_li]:before:size-1.5 [&_li]:before:bg-gold-500 [&_li]:before:content-['']",
        '[&_a]:underline [&_a]:underline-offset-4',
        $isDark
            ? 'text-mist [&_h3]:text-cream-bright [&_strong]:text-cream-bright [&_a]:text-gold-300'
            : 'text-ink-soft [&_h3]:text-navy-900 [&_strong]:text-ink [&_a]:text-gold-700',
    ])->implode(' ');
@endphp

<section class="{{ $surface }}">
    <div class="mx-auto grid max-w-[1200px] items-center gap-10 px-6 py-16 lg:grid-cols-2 lg:gap-20 lg:px-0 lg:py-28">
        @if ($image)
            <div @class(['relative', 'lg:order-2' => ($imageSide ?? 'start') === 'end'])>
                <img src="{{ $image }}" alt="{{ $imageAlt }}" class="h-72 w-full object-cover sm:h-96 lg:h-[520px]" loading="lazy">
                <span class="pointer-events-none absolute -top-5 -end-5 size-24 border-t border-e border-gold-500" aria-hidden="true"></span>
            </div>
        @endif

        <div class="flex flex-col gap-6">
            @include('blocks.partials.section-heading', [
                'tone' => $isDark ? 'dark' : 'light',
                'size' => 'md',
                'lead' => null,
            ])

            @if (filled($body))
                <div class="{!! $prose !!}">
                    {!! $body !!}
                </div>
            @endif

            @if ($linkUrl && $linkLabel)
                <a href="{{ $linkUrl }}" @class([
                    'inline-flex items-center gap-2.5 pt-2 text-sm font-bold tracking-wide',
                    'text-gold-300 hover:text-gold-400' => $isDark,
                    'text-navy-900 hover:text-gold-700' => ! $isDark,
                ])>
                    {{ $linkLabel }}
                    <svg class="size-4 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            @endif
        </div>
    </div>
</section>
