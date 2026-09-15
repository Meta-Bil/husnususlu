@php
    use App\Blocks\BlockRegistry;
    use App\Support\Localization\LocaleUrls;

    $locale = app()->getLocale();
@endphp

<x-layouts.app
    :title="$title"
    :description="$description"
    :canonical="$canonical"
    :alternates="$alternates"
    :noindex="$noindex"
>
    @if ($page)
        {!! BlockRegistry::render($page->blocks) !!}
    @else
        <section class="bg-navy-900 text-cream-bright">
            <div class="mx-auto max-w-[1200px] px-6 py-20 lg:px-0">
                <h1 class="font-display text-5xl font-medium lg:text-7xl">{{ $kind->getLabel() }}</h1>
            </div>
        </section>
    @endif

    <section class="bg-ivory">
        <div class="mx-auto grid max-w-[1200px] gap-x-14 gap-y-0 px-6 py-20 md:grid-cols-2 lg:px-0">
            @foreach ($treatments as $treatment)
                <a href="{{ LocaleUrls::treatment($treatment) }}" class="flex items-center gap-6 border-b border-line py-5 transition hover:text-gold-700">
                    <span class="font-display text-xl text-gold-700">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="grow text-lg text-navy-900">{{ $treatment->localized('title') }}</span>
                    <span class="text-gold-700 rtl:-scale-x-100">&rarr;</span>
                </a>
            @endforeach
        </div>
    </section>
</x-layouts.app>
