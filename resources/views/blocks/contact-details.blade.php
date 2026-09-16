@php
    /** @var string|null $address */
    /** @var string|null $workingHours */
    $rows = array_values(array_filter([
        $clinicPhone ? ['key' => 'phone', 'label' => __('front.phone'), 'value' => $clinicPhone, 'url' => 'tel:'.preg_replace('/[^\d+]/', '', $clinicPhone)] : null,
        $whatsappPhone ? ['key' => 'whatsapp', 'label' => 'WhatsApp', 'value' => $whatsappPhone, 'url' => $whatsappUrl, 'external' => true] : null,
        $email ? ['key' => 'email', 'label' => __('front.email'), 'value' => $email, 'url' => 'mailto:'.$email] : null,
        $workingHours ? ['key' => 'hours', 'label' => __('front.working_hours'), 'value' => $workingHours, 'url' => null] : null,
        $address ? ['key' => 'address', 'label' => __('front.address'), 'value' => $address, 'url' => null, 'wide' => true] : null,
    ]));
@endphp

<section class="bg-ivory">
    <div class="mx-auto flex max-w-[1200px] flex-col gap-10 px-6 py-16 lg:px-0 lg:py-28">
        @include('blocks.partials.section-heading', ['size' => 'md', 'lead' => null])

        <div class="grid gap-10 lg:grid-cols-2 lg:gap-16">
            <dl class="grid gap-x-8 border-t border-line sm:grid-cols-2">
                @foreach ($rows as $row)
                    <div @class([
                        'flex flex-col gap-2.5 border-b border-line py-6',
                        'sm:col-span-2' => $row['wide'] ?? false,
                    ])>
                        <dt class="flex items-center gap-2.5 text-[11.5px] font-semibold tracking-[0.22em] text-gold-700 uppercase">
                            @switch($row['key'])
                                @case('phone')
                                    <svg class="size-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>
                                    @break
                                @case('whatsapp')
                                    <svg class="size-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.3 7.4L3 21l2.1-5.6A8.5 8.5 0 1 1 21 11.5z"/></svg>
                                    @break
                                @case('email')
                                    <svg class="size-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                                    @break
                                @case('hours')
                                    <svg class="size-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                    @break
                                @default
                                    <svg class="size-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
                            @endswitch
                            {{ $row['label'] }}
                        </dt>
                        <dd class="font-display text-2xl leading-snug font-semibold text-navy-900 sm:text-[26px]">
                            @if ($row['url'])
                                <a href="{{ $row['url'] }}" @if ($row['external'] ?? false) target="_blank" rel="noopener" @endif class="hover:text-gold-700">{{ $row['value'] }}</a>
                            @else
                                {{ $row['value'] }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>

            @if ($showMap)
                <div class="flex flex-col bg-navy-900">
                    <div class="relative h-60 overflow-hidden">
                        <svg viewBox="0 0 520 240" preserveAspectRatio="xMidYMid slice" class="block h-60 w-full" aria-hidden="true">
                            <rect width="520" height="240" class="fill-navy-900"/>
                            <g transform="rotate(-16 260 120)" fill="none" stroke-linecap="round">
                                <g class="stroke-gold-400/10" stroke-width="1">
                                    <path d="M-120 -140V380M-74 -140V380M-28 -140V380M18 -140V380M64 -140V380M110 -140V380M156 -140V380M202 -140V380M248 -140V380M294 -140V380M340 -140V380M386 -140V380M432 -140V380M478 -140V380M524 -140V380M570 -140V380M616 -140V380"/>
                                    <path d="M-140 -120H660M-140 -80H660M-140 -40H660M-140 0H660M-140 40H660M-140 80H660M-140 120H660M-140 190H660M-140 230H660M-140 270H660M-140 310H660"/>
                                </g>
                                <g class="stroke-gold-400/20" stroke-width="3">
                                    <path d="M110 -140V380M432 -140V380M-140 40H660"/>
                                </g>
                                <rect x="302" y="58" width="72" height="52" class="fill-gold-400/5" stroke="none"/>
                                <rect x="18" y="200" width="92" height="30" class="fill-gold-400/5" stroke="none"/>
                                <path d="M268 -140V380" class="stroke-gold-400/25" stroke-width="5"/>
                                <path d="M-140 150H660" class="stroke-gold-500/40" stroke-width="9"/>
                            </g>
                            <ellipse cx="268" cy="150" rx="16" ry="5" class="fill-gold-500/30"/>
                            <circle cx="268" cy="121" r="27" fill="none" class="stroke-gold-500/35" stroke-width="1"/>
                            <path d="M268 149c-2.5-7-15-15.5-15-28a15 15 0 0 1 30 0c0 12.5-12.5 21-15 28z" class="fill-gold-500"/>
                            <circle cx="268" cy="121" r="5.5" class="fill-navy-900"/>
                        </svg>

                        @if ($mapLabel)
                            <span class="absolute top-24 start-1/2 border-s-2 border-gold-500 bg-navy-950 px-3.5 py-2 text-[12.5px] font-semibold whitespace-nowrap text-cream-bright">{{ $mapLabel }}</span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-4 border-t border-gold-400/20 px-6 py-4">
                        @if ($mapUrl)
                            <a href="{{ $mapUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2.5 text-sm font-semibold text-gold-300 hover:text-gold-400">
                                {{ __('front.directions') }}
                                <svg class="size-4 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        @endif

                        @if ($mapArea)
                            <span class="text-[12.5px] text-mist-dim">{{ $mapArea }}</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
