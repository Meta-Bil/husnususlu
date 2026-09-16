@php
    /** @var array<int, array{number: string, title: string|null, url: string|null}> $items */
    /** @var array<int, array{title: string|null, url: string|null}> $procedures */
@endphp

@if (filled($items) || filled($procedures))
    <section class="bg-navy-900">
        <div class="mx-auto grid max-w-[1200px] gap-12 px-6 py-16 lg:grid-cols-[400px_minmax(0,1fr)] lg:gap-24 lg:px-0 lg:py-28">
            @include('blocks.partials.section-heading', ['tone' => 'dark'])

            <div class="flex flex-col gap-10">
                @if (filled($items))
                    <ul class="md:columns-2 md:gap-x-14">
                        @foreach ($items as $item)
                            <li class="break-inside-avoid border-b border-gold-400/20">
                                @php($tag = $item['url'] ? 'a' : 'span')
                                <{{ $tag }} @if ($item['url']) href="{{ $item['url'] }}" @endif class="flex items-center gap-5 py-5 text-cream transition-colors hover:text-gold-300">
                                    <span class="font-display w-7 shrink-0 text-xl text-gold-500">{{ $item['number'] }}</span>
                                    <span class="grow text-[17px] leading-snug">{{ $item['title'] }}</span>
                                    <svg class="size-4 shrink-0 text-gold-500 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </{{ $tag }}>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if (filled($procedures))
                    <div class="flex flex-col gap-4">
                        <p class="text-[11.5px] font-semibold tracking-[0.3em] text-mist-dim uppercase">{{ $proceduresLabel }}</p>
                        <ul class="flex flex-wrap gap-2.5">
                            @foreach ($procedures as $procedure)
                                <li>
                                    @php($tag = $procedure['url'] ? 'a' : 'span')
                                    <{{ $tag }} @if ($procedure['url']) href="{{ $procedure['url'] }}" @endif class="inline-flex border border-gold-400/35 px-4 py-2.5 text-[13.5px] text-mist transition-colors hover:border-gold-500 hover:text-gold-300">{{ $procedure['title'] }}</{{ $tag }}>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif
