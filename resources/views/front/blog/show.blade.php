@php
    use App\Support\Localization\LocaleUrls;
    use App\Support\Seo\Schema;

    $locale = app()->getLocale();
    $body = $post->localized('body');
    $faq = collect($post->faq ?? [])
        ->map(fn (array $row): array => [
            'question' => $row['question'][$locale] ?? $row['question']['tr'] ?? null,
            'answer' => $row['answer'][$locale] ?? $row['answer']['tr'] ?? null,
        ])
        ->filter(fn (array $row): bool => filled($row['question']) && filled($row['answer']))
        ->values();

    $graph = [Schema::forPost($post)];

    if ($faq->isNotEmpty()) {
        $graph[] = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faq->map(fn (array $row): array => [
                '@type' => 'Question',
                'name' => $row['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags((string) $row['answer'])],
            ])->all(),
        ];
    }
@endphp

<x-layouts.app
    :title="$title"
    :description="$description"
    :canonical="$canonical"
    :alternates="$alternates"
    :noindex="$noindex"
    :json-ld="$graph"
>
    <x-ui.breadcrumbs :items="array_values(array_filter([
        ['label' => __('front.home'), 'url' => route($locale.'.home')],
        ['label' => __('front.all_posts'), 'url' => route($locale.'.blog.index')],
        $post->category ? ['label' => $post->category->localized('name'), 'url' => LocaleUrls::category($post->category)] : null,
        ['label' => $post->localized('title')],
    ]))" />

    <article>
        <header class="bg-navy-900 text-cream-bright">
            <div class="mx-auto flex max-w-[1200px] flex-col gap-6 px-6 pt-10 pb-16 lg:px-0 lg:pb-20">
                <p class="text-xs font-semibold tracking-[0.3em] text-gold-500 uppercase">{{ $post->category?->localized('name') }}</p>
                <h1 class="max-w-4xl font-display text-4xl leading-tight font-medium lg:text-6xl">{{ $post->localized('title') }}</h1>
                <div class="flex flex-wrap items-center gap-4 text-sm text-mist-dim">
                    <span>{{ $post->published_at?->translatedFormat('j F Y') }}</span>
                    <span aria-hidden="true">·</span>
                    <span>{{ __('front.reading_time', ['minutes' => $post->readingTime()]) }}</span>
                    <span aria-hidden="true">·</span>
                    <span class="text-cream">{{ config('app.name') }}</span>
                </div>
                @if ($post->localized('excerpt'))
                    <p class="max-w-3xl text-lg leading-relaxed text-mist">{{ $post->localized('excerpt') }}</p>
                @endif
            </div>
        </header>

        <div class="bg-paper">
            <div class="mx-auto max-w-[1200px] px-6 py-16 lg:px-0 lg:py-20">
                <div class="prose-article max-w-3xl text-[18px] leading-[1.8] text-ink-soft">
                    {!! $body !!}
                </div>

                @if ($faq->isNotEmpty())
                    <section class="mt-16 max-w-3xl">
                        <h2 class="font-display text-4xl text-navy-900">{{ __('front.contents') }}</h2>
                        <div class="mt-8 flex flex-col">
                            @foreach ($faq as $row)
                                <details class="border-b border-line py-6" @if ($loop->first) open @endif>
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-8 text-lg font-semibold text-navy-900">
                                        {{ $row['question'] }}
                                        <svg class="size-5 shrink-0 text-gold-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                                    </summary>
                                    <div class="pt-4 text-base leading-relaxed text-ink-muted">{!! $row['answer'] !!}</div>
                                </details>
                            @endforeach
                        </div>
                    </section>
                @endif

                <aside class="mt-16 flex max-w-3xl flex-col gap-4 border-t-2 border-navy-900 pt-8 sm:flex-row sm:items-center sm:gap-8">
                    <div class="flex flex-col gap-2">
                        <span class="font-display text-2xl text-navy-900">{{ config('app.name') }}</span>
                        <span class="text-sm text-gold-700">{{ __('front.doctor_title') }}</span>
                        <span class="text-sm text-ink-muted">{{ __('front.prepared_by') }}</span>
                        <span class="text-xs text-mist-dim">{{ __('front.last_updated') }}: {{ $post->updated_at?->translatedFormat('j F Y') }}</span>
                    </div>
                </aside>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <section class="bg-ivory">
                <div class="mx-auto flex max-w-[1200px] flex-col gap-10 px-6 py-16 lg:px-0">
                    <h2 class="font-display text-4xl text-navy-900">{{ __('front.related_posts') }}</h2>
                    <div class="grid gap-10 md:grid-cols-3">
                        @foreach ($related as $item)
                            <a href="{{ LocaleUrls::post($item) }}" class="flex flex-col gap-3 border-t-2 border-navy-900 pt-6">
                                <span class="text-xs font-semibold tracking-[0.16em] text-gold-700 uppercase">{{ $item->category?->localized('name') }}</span>
                                <span class="font-display text-2xl leading-tight text-navy-900">{{ $item->localized('title') }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </article>
</x-layouts.app>
