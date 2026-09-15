@props([
    'href' => null,
    'variant' => 'gold',
    'icon' => null,
    'target' => null,
])

@php
    $classes = match ($variant) {
        'gold' => 'bg-gold-500 text-navy-900 hover:bg-gold-400',
        'outline' => 'border border-cream-bright/35 text-cream-bright hover:border-gold-500 hover:text-gold-300',
        'dark' => 'bg-navy-900 text-cream-bright hover:bg-navy-800',
        'ghost' => 'border border-line text-ink hover:border-gold-500 hover:text-gold-700',
        default => 'bg-gold-500 text-navy-900 hover:bg-gold-400',
    };

    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    @if ($target) target="{{ $target }}" rel="noopener" @endif
    {{ $attributes->class(['inline-flex h-14 items-center gap-3 px-7 text-[15px] font-bold tracking-wide transition-colors', $classes]) }}
>
    @if ($icon === 'whatsapp')
        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 11.5a8.4 8.4 0 0 1-12.3 7.4L3 21l2.1-5.6A8.5 8.5 0 1 1 21 11.5z"/>
        </svg>
    @endif

    {{ $slot }}

    @if ($icon === 'arrow')
        <svg class="size-[18px] rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M5 12h14M13 6l6 6-6 6"/>
        </svg>
    @endif
</{{ $tag }}>
