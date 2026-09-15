@props([
    'items' => [],
])

@if (filled($items))
    <nav aria-label="breadcrumb" class="bg-navy-900">
        <ol class="mx-auto flex max-w-[1200px] flex-wrap items-center gap-2 px-6 pt-6 text-[13px] text-mist-dim lg:px-0">
            @foreach ($items as $item)
                <li class="flex items-center gap-2">
                    @if (! $loop->first)
                        <span class="text-gold-500" aria-hidden="true">/</span>
                    @endif

                    @if (($item['url'] ?? null) && ! $loop->last)
                        <a href="{{ $item['url'] }}" class="hover:text-cream">{{ $item['label'] }}</a>
                    @else
                        <span class="text-cream">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    @push('head')
        <script type="application/ld+json">{!! json_encode(\App\Support\Seo\Schema::breadcrumbs($items), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush
@endif
