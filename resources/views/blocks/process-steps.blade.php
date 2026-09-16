@php
    /** @var string|null $image */
    /** @var array<int, array{number: string, title: string|null, text: string|null}> $steps */
@endphp

@if (filled($steps) || filled($title))
    <section class="bg-ivory">
        <div class="mx-auto flex max-w-[1200px] flex-col gap-12 px-6 py-16 lg:gap-14 lg:px-0 lg:py-28">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-end lg:gap-20">
                @include('blocks.partials.section-heading', ['showLead' => false])

                @if (filled($lead))
                    <p class="text-[17px] leading-relaxed text-ink-muted text-pretty">{{ $lead }}</p>
                @endif
            </div>

            @if ($image)
                <img src="{{ $image }}" alt="{{ $title }}" class="h-56 w-full object-cover sm:h-80 lg:h-[380px]" loading="lazy">
            @endif

            @if (filled($steps))
                <ol class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($steps as $step)
                        <li class="flex flex-col gap-3.5 border-t-2 border-navy-900 pt-5">
                            <span class="font-display text-[28px] leading-none text-gold-700 not-italic ltr:italic">{{ $step['number'] }}</span>
                            <span class="text-lg font-bold text-navy-900">{{ $step['title'] }}</span>
                            <span class="text-[15px] leading-relaxed text-ink-muted">{{ $step['text'] }}</span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </section>
@endif
