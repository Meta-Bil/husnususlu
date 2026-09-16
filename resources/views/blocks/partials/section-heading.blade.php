@php
    /**
     * Shared block heading: eyebrow, serif title with an optional gold accent
     * phrase and a lead paragraph.
     *
     * @var string|null $eyebrow
     * @var string|null $title
     * @var string|null $accent
     * @var string|null $lead
     */
    $headingTone = $tone ?? 'light';
    $headingSize = $size ?? 'lg';
    $headingEyebrow = $eyebrow ?? null;
    $headingTitle = $title ?? null;
    $headingAccent = $accent ?? null;
    $headingLead = ($showLead ?? true) ? ($lead ?? null) : null;
    $isDark = $headingTone === 'dark';
@endphp

@if (filled($headingEyebrow) || filled($headingTitle) || filled($headingLead))
    <div class="flex flex-col gap-5">
        @if (filled($headingEyebrow))
            <p @class([
                'text-xs font-semibold tracking-[0.3em] uppercase',
                'text-gold-500' => $isDark,
                'text-gold-700' => ! $isDark,
            ])>{{ $headingEyebrow }}</p>
        @endif

        @if (filled($headingTitle))
            <h2 @class([
                'font-display font-medium leading-[1.04] text-balance',
                'text-4xl sm:text-5xl lg:text-6xl' => $headingSize === 'lg',
                'text-3xl sm:text-4xl lg:text-5xl' => $headingSize === 'md',
                'text-cream-bright' => $isDark,
                'text-navy-900' => ! $isDark,
            ])>
                {!! nl2br(e($headingTitle)) !!}@if (filled($headingAccent))
                    <em @class([
                        'font-medium not-italic ltr:italic',
                        'text-gold-400' => $isDark,
                        'text-gold-700' => ! $isDark,
                    ])>{{ $headingAccent }}</em>
                @endif
            </h2>
        @endif

        @if (filled($headingLead))
            <p @class([
                'max-w-2xl text-[17px] leading-relaxed text-pretty',
                'text-mist-muted' => $isDark,
                'text-ink-muted' => ! $isDark,
            ])>{{ $headingLead }}</p>
        @endif
    </div>
@endif
