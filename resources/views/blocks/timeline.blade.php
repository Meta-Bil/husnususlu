@php
    /** @var array<int, array{label: string|null, items: array<int, array{year: string|null, title: string|null, institution: string|null}>}> $groups */
    $groups = array_values(array_filter($groups, fn (array $group): bool => filled($group['items'])));
@endphp

@if (filled($groups))
    <section class="bg-paper">
        <div class="mx-auto flex max-w-[1200px] flex-col gap-12 px-6 py-16 lg:gap-16 lg:px-0 lg:py-28">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_460px] lg:items-end lg:gap-20">
                @include('blocks.partials.section-heading', ['showLead' => false])

                @if (filled($lead))
                    <p class="text-[17px] leading-relaxed text-ink-muted text-pretty">{{ $lead }}</p>
                @endif
            </div>

            <div @class([
                'grid items-start gap-12 lg:gap-20',
                'md:grid-cols-2' => count($groups) > 1,
            ])>
                @foreach ($groups as $group)
                    <div class="flex flex-col gap-7">
                        @if ($group['label'])
                            <p class="border-b-2 border-navy-900 pb-3.5 text-xs font-bold tracking-[0.24em] text-navy-900 uppercase">{{ $group['label'] }}</p>
                        @endif

                        <ol class="flex flex-col gap-6 border-s border-line">
                            @foreach ($group['items'] as $item)
                                <li class="relative flex flex-col gap-1.5 ps-8">
                                    <span class="absolute top-2 -start-[3.5px] size-[7px] bg-gold-500" aria-hidden="true"></span>
                                    <span class="font-display text-[22px] leading-tight font-semibold text-gold-700">{{ $item['year'] }}</span>
                                    <span class="text-[16.5px] leading-snug font-semibold text-navy-900">{{ $item['title'] }}</span>
                                    @if ($item['institution'])
                                        <span class="text-[14.5px] leading-relaxed text-ink-muted">{{ $item['institution'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
