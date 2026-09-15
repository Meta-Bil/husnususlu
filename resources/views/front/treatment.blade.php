@php
    use App\Blocks\BlockRegistry;
    use App\Support\Localization\LocaleUrls;
    use App\Support\Localization\Locales;
    use App\Support\Seo\Schema;

    $locale = app()->getLocale();
    $segment = $treatment->kind->segmentKey();
    $graph = array_merge(
        [Schema::forTreatment($treatment)],
        BlockRegistry::jsonLd($treatment->blocks),
    );
@endphp

<x-layouts.app
    :title="$title"
    :description="$description"
    :canonical="$canonical"
    :alternates="$alternates"
    :noindex="$noindex"
    :json-ld="$graph"
>
    <x-ui.breadcrumbs :items="[
        ['label' => __('front.home'), 'url' => route($locale.'.home')],
        ['label' => $treatment->kind->getLabel(), 'url' => route($locale.'.'.$segment.'.index')],
        ['label' => $treatment->localized('title')],
    ]" />

    {!! BlockRegistry::render($treatment->blocks) !!}

    @if ($related->isNotEmpty())
        <section class="bg-paper">
            <div class="mx-auto flex max-w-[1200px] flex-col gap-10 px-6 py-20 lg:px-0">
                <h2 class="font-display text-4xl text-navy-900">{{ __('front.related_treatments') }}</h2>
                <div class="grid gap-8 md:grid-cols-3">
                    @foreach ($related as $item)
                        <a href="{{ LocaleUrls::treatment($item) }}" class="flex flex-col gap-4 border border-line bg-white p-9 transition hover:border-gold-500">
                            <span class="font-display text-2xl leading-tight text-navy-900">{{ $item->localized('title') }}</span>
                            <span class="text-[15px] leading-relaxed text-ink-muted">{{ $item->localized('summary') }}</span>
                            <span class="pt-2 text-sm font-semibold text-navy-900">{{ __('front.details') }} &rarr;</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.app>
