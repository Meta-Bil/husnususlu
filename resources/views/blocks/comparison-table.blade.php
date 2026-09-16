@php
    /** @var string|null $columnA */
    /** @var string|null $columnB */
    /** @var array<int, array{label: string|null, value_a: string|null, value_b: string|null}> $rows */
@endphp

@if (filled($rows))
    <section class="bg-ivory">
        <div class="mx-auto grid max-w-[1200px] gap-12 px-6 py-16 lg:grid-cols-[360px_minmax(0,1fr)] lg:gap-20 lg:px-0 lg:py-28">
            @include('blocks.partials.section-heading', ['size' => 'md'])

            <div class="flex flex-col">
                <div class="hidden grid-cols-[220px_minmax(0,1fr)_minmax(0,1fr)] gap-x-6 border-b border-navy-900 pb-3.5 text-xs font-bold tracking-[0.18em] text-navy-900 uppercase sm:grid">
                    <span></span>
                    <span class="text-gold-700">{{ $columnA }}</span>
                    <span>{{ $columnB }}</span>
                </div>

                @foreach ($rows as $row)
                    <div class="grid grid-cols-2 gap-x-6 gap-y-3 border-b border-line py-5 text-base text-ink sm:grid-cols-[220px_minmax(0,1fr)_minmax(0,1fr)] sm:items-baseline">
                        <span class="col-span-2 text-ink-muted sm:col-span-1">{{ $row['label'] }}</span>

                        <span class="flex flex-col gap-1">
                            <span class="text-[11px] font-semibold tracking-[0.18em] text-gold-700 uppercase sm:hidden">{{ $columnA }}</span>
                            <span class="font-semibold">{{ $row['value_a'] }}</span>
                        </span>

                        <span class="flex flex-col gap-1">
                            <span class="text-[11px] font-semibold tracking-[0.18em] text-ink-muted uppercase sm:hidden">{{ $columnB }}</span>
                            <span>{{ $row['value_b'] }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
