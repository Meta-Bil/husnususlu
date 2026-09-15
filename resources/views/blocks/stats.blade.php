@php
    /** @var array<int, array{value: string|null, label: string|null}> $items */
@endphp

@if (filled($items))
    <section class="bg-navy-900">
        <div class="mx-auto max-w-[1200px] border-y border-gold-400/30 px-6 lg:px-0">
            <dl class="grid grid-cols-2 lg:grid-cols-{{ count($items) }}">
                @foreach ($items as $item)
                    <div @class([
                        'flex flex-col items-center gap-2 py-8 text-center',
                        'border-e border-gold-400/20' => ! $loop->last,
                        'border-b border-gold-400/20 lg:border-b-0' => $loop->index < count($items) - 2,
                        'max-lg:!border-e-0' => $loop->index % 2 === 1,
                    ])>
                        <dt class="font-display text-4xl leading-none text-gold-400 lg:text-6xl">{{ $item['value'] }}</dt>
                        <dd class="text-[11px] font-semibold tracking-[0.22em] text-mist-muted uppercase">{{ $item['label'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>
@endif
