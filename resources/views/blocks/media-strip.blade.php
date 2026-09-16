@php
    /** @var string|null $label */
    /** @var array<int, string> $channels */
@endphp

@if (filled($channels))
    <section class="bg-navy-900">
        <div class="mx-auto flex max-w-[1200px] flex-wrap items-center gap-x-12 gap-y-5 px-6 py-9 lg:px-0 lg:py-11">
            <p class="text-[11.5px] font-semibold tracking-[0.3em] whitespace-nowrap text-gold-500 uppercase">{{ $label }}</p>
            <span class="hidden h-px grow bg-gold-400/20 sm:block" aria-hidden="true"></span>
            <ul class="flex flex-wrap items-center gap-x-10 gap-y-3 sm:gap-x-14">
                @foreach ($channels as $channel)
                    <li class="text-lg font-bold tracking-wide text-cream/60 sm:text-[22px]">{{ $channel }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
