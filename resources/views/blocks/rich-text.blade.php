@php
    /** @var string $body */
    /** @var array<int, array{id: string, text: string}> $headings */
    $isDark = ($background ?? 'paper') === 'navy';

    $surface = match ($background ?? 'paper') {
        'ivory' => 'bg-ivory',
        'navy' => 'bg-navy-900',
        default => 'bg-paper',
    };

    $prose = collect([
        'flex flex-col gap-5',
        '[&_h2]:font-display [&_h2]:text-3xl [&_h2]:leading-tight [&_h2]:font-medium [&_h2]:pt-6 lg:[&_h2]:text-[44px]',
        '[&_h3]:text-xl [&_h3]:leading-snug [&_h3]:font-bold [&_h3]:pt-3',
        '[&_p]:text-[17px] [&_p]:leading-[1.8] [&_p]:text-pretty lg:[&_p]:text-lg',
        '[&_ul]:flex [&_ul]:flex-col [&_ul]:gap-3 [&_ol]:flex [&_ol]:flex-col [&_ol]:gap-3',
        "[&_li]:relative [&_li]:ps-6 [&_li]:text-[17px] [&_li]:leading-[1.7] [&_li]:before:absolute [&_li]:before:start-0 [&_li]:before:top-[0.65em] [&_li]:before:size-1.5 [&_li]:before:bg-gold-500 [&_li]:before:content-['']",
        '[&_a]:underline [&_a]:underline-offset-4',
        '[&_img]:h-auto [&_img]:w-full',
        '[&_blockquote]:font-display [&_blockquote]:border-s-2 [&_blockquote]:border-gold-500 [&_blockquote]:ps-7 [&_blockquote]:text-2xl [&_blockquote]:leading-snug [&_blockquote]:not-italic lg:[&_blockquote]:text-3xl ltr:[&_blockquote]:italic',
        $isDark
            ? 'text-mist [&_h2]:text-cream-bright [&_h3]:text-cream-bright [&_p]:text-mist [&_strong]:text-cream-bright [&_a]:text-gold-300 [&_blockquote]:text-cream-bright'
            : 'text-ink-soft [&_h2]:text-navy-900 [&_h3]:text-navy-900 [&_p]:text-ink-soft [&_strong]:text-ink [&_a]:text-gold-700 [&_blockquote]:text-navy-900',
    ])->implode(' ');

    $hasToc = filled($headings);
@endphp

@if (filled($body))
    <section class="{{ $surface }}">
        <div @class([
            'mx-auto max-w-[1200px] px-6 py-16 lg:px-0 lg:py-28',
            'grid gap-12 lg:grid-cols-[260px_minmax(0,1fr)] lg:gap-24' => $hasToc,
        ])>
            @if ($hasToc)
                <div class="flex flex-col gap-10 lg:sticky lg:top-24 lg:self-start">
                    <nav class="flex flex-col gap-4" aria-label="{{ $tocLabel }}">
                        <p @class([
                            'text-xs font-semibold tracking-[0.3em] uppercase',
                            'text-gold-500' => $isDark,
                            'text-gold-700' => ! $isDark,
                        ])>{{ $tocLabel }}</p>
                        <ul class="flex flex-col">
                            @foreach ($headings as $heading)
                                <li @class([
                                    'border-s',
                                    'border-gold-400/25' => $isDark,
                                    'border-line' => ! $isDark,
                                ])>
                                    <a href="#{{ $heading['id'] }}" @class([
                                        'block py-2.5 ps-4.5 text-[15px] transition-colors',
                                        'text-mist-muted hover:text-gold-300' => $isDark,
                                        'text-ink-muted hover:text-gold-700' => ! $isDark,
                                    ])>{{ $heading['text'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>

                    @if (filled($aside))
                        <div @class([
                            'flex flex-col gap-3 border-t pt-6 text-[14.5px] leading-relaxed',
                            'border-gold-400/25 text-mist-muted' => $isDark,
                            'border-line text-ink-muted' => ! $isDark,
                        ])>
                            <p>{{ $aside }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="flex max-w-[760px] flex-col gap-6">
                @include('blocks.partials.section-heading', [
                    'tone' => $isDark ? 'dark' : 'light',
                    'size' => 'md',
                    'lead' => null,
                ])

                <div class="{!! $prose !!}">
                    {!! $body !!}
                </div>
            </div>
        </div>
    </section>
@endif
