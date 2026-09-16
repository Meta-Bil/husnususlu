@props([
    'video',
    'size' => 'default',
])

@php
    $isLarge = $size === 'large';
    $label = collect([$video->channel, $video->localized('program')])->filter()->implode(' · ');
@endphp

<a href="{{ $video->watchUrl() }}"
   target="_blank"
   rel="noopener"
   {{ $attributes->class(['group flex flex-col gap-4']) }}>
    <div class="relative aspect-video overflow-hidden bg-navy-800">
        <img src="{{ $video->thumbnailUrl() }}"
             alt="{{ $video->localized('title') }}"
             loading="lazy"
             class="size-full object-cover transition duration-500 group-hover:scale-105 {{ $isLarge ? '' : 'grayscale-[0.85] brightness-[0.85] group-hover:grayscale-0 group-hover:brightness-100' }}">

        <span @class([
            'absolute bottom-5 start-5 flex items-center justify-center rounded-full bg-gold-500 text-navy-900',
            'size-16' => $isLarge,
            'size-13' => ! $isLarge,
        ])>
            <svg class="{{ $isLarge ? 'size-6' : 'size-[18px]' }} rtl:-scale-x-100" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M8 5v14l11-7z" fill="currentColor"/>
            </svg>
        </span>

        @if ($video->duration)
            <span class="absolute bottom-4 end-4 bg-navy-900/80 px-2 py-1 text-xs text-cream-bright">{{ $video->duration }}</span>
        @endif
    </div>

    @if ($label)
        <span class="text-[11.5px] font-semibold tracking-[0.24em] text-gold-500 uppercase">{{ $label }}</span>
    @endif

    <span @class([
        'font-display leading-snug text-cream',
        'text-3xl' => $isLarge,
        'text-2xl' => ! $isLarge,
    ])>{{ $video->localized('title') }}</span>
</a>
