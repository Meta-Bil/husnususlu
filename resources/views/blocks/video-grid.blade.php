@php
    /** @var array<string, mixed>|null $featured */
    /** @var array<int, array<string, mixed>> $items */
@endphp

@if ($featured || filled($items))
    <section class="bg-navy-900">
        <div class="mx-auto flex max-w-[1200px] flex-col gap-10 px-6 py-16 lg:gap-12 lg:px-0 lg:py-28">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between sm:gap-10">
                @include('blocks.partials.section-heading', ['tone' => 'dark', 'size' => 'md', 'lead' => null])

                @if ($linkUrl && $linkLabel)
                    <a href="{{ $linkUrl }}" class="inline-flex shrink-0 items-center gap-2.5 text-[14.5px] font-semibold text-gold-300 hover:text-gold-400">
                        {{ $linkLabel }}
                        <svg class="size-4 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                @endif
            </div>

            @if ($featured)
                <a href="{{ $featured['url'] }}" target="_blank" rel="noopener" class="group grid gap-8 lg:grid-cols-[minmax(0,720px)_minmax(0,1fr)] lg:items-center lg:gap-16">
                    <div class="relative aspect-video overflow-hidden bg-navy-800">
                        <img src="{{ $featured['thumbnail'] }}" alt="{{ $featured['title'] }}" class="size-full object-cover brightness-[0.8] grayscale-[0.85] transition duration-500 group-hover:brightness-100 group-hover:grayscale-0" loading="lazy">
                        <span class="absolute bottom-5 start-5 grid size-14 place-items-center rounded-full bg-gold-500 text-navy-900 sm:size-[68px]" aria-hidden="true">
                            <svg class="size-5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                        </span>
                        @if ($featured['duration'])
                            <span class="absolute bottom-4 end-4 bg-navy-900/80 px-2.5 py-1 text-xs text-cream-bright">{{ $featured['duration'] }}</span>
                        @endif
                    </div>

                    <div class="flex flex-col gap-4">
                        @if ($featured['eyebrow'])
                            <p class="text-[11.5px] font-semibold tracking-[0.3em] text-gold-500 uppercase">{{ $featured['eyebrow'] }}</p>
                        @endif
                        <h3 class="font-display text-3xl leading-tight font-medium text-cream-bright lg:text-[46px]">{{ $featured['title'] }}</h3>
                        @if ($featured['description'])
                            <p class="text-[17px] leading-relaxed text-mist-muted text-pretty">{{ $featured['description'] }}</p>
                        @endif
                        <div class="flex flex-wrap items-center gap-4 text-[11.5px] font-semibold tracking-[0.24em] text-mist-dim uppercase">
                            <span>YouTube</span>
                            @if ($featured['duration'])
                                <span class="h-3.5 w-px bg-gold-400/30" aria-hidden="true"></span>
                                <span>{{ $featured['duration'] }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endif

            @if (filled($items))
                <ul class="grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($items as $item)
                        <li>
                            <a href="{{ $item['url'] }}" target="_blank" rel="noopener" class="group flex flex-col gap-4">
                                <span class="relative block aspect-video overflow-hidden bg-navy-800">
                                    <img src="{{ $item['thumbnail'] }}" alt="{{ $item['title'] }}" class="size-full object-cover brightness-[0.8] grayscale-[0.85] transition duration-500 group-hover:brightness-100 group-hover:grayscale-0" loading="lazy">
                                    <span class="absolute bottom-5 start-5 grid size-13 place-items-center rounded-full bg-gold-500 text-navy-900" aria-hidden="true">
                                        <svg class="size-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                    </span>
                                    @if ($item['duration'])
                                        <span class="absolute bottom-4 end-4 bg-navy-900/80 px-2 py-1 text-xs text-cream-bright">{{ $item['duration'] }}</span>
                                    @endif
                                </span>

                                @if ($item['eyebrow'])
                                    <span class="text-[11.5px] font-semibold tracking-[0.24em] text-gold-500 uppercase">{{ $item['eyebrow'] }}</span>
                                @endif

                                <span class="font-display text-2xl leading-tight text-cream">{{ $item['title'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
@endif
