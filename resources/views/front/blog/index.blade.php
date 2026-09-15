@php
    use App\Support\Localization\LocaleUrls;

    $locale = app()->getLocale();
    $featured = $posts->currentPage() === 1 ? $posts->first() : null;
    $rest = $featured ? $posts->slice(1) : $posts;
@endphp

<x-layouts.app
    :title="$title"
    :description="$description"
    :canonical="$canonical"
    :alternates="$alternates"
    :noindex="$noindex"
>
    <section class="bg-navy-900 text-cream-bright">
        <div class="mx-auto flex max-w-[1200px] flex-col gap-5 px-6 py-16 lg:px-0 lg:py-20">
            <p class="text-xs font-semibold tracking-[0.3em] text-gold-500 uppercase">{{ $category?->localized('name') ?? __('front.all_posts') }}</p>
            <h1 class="font-display text-5xl leading-tight font-medium lg:text-7xl">
                {{ $category?->localized('name') ?? config('app.name') }}
            </h1>
            @if ($category?->localized('description'))
                <p class="max-w-2xl text-lg leading-relaxed text-mist">{{ $category->localized('description') }}</p>
            @endif
        </div>
    </section>

    <section class="bg-ivory">
        <div class="mx-auto flex max-w-[1200px] flex-col gap-12 px-6 py-16 lg:px-0 lg:py-20">
            <div class="flex flex-wrap gap-2.5">
                <a href="{{ route($locale.'.blog.index') }}"
                   @class([
                       'border px-4 py-2.5 text-sm transition',
                       'border-navy-900 bg-navy-900 text-cream-bright' => ! $category,
                       'border-line text-ink-muted hover:border-gold-500' => $category,
                   ])>
                    {{ __('front.all') }} ({{ $posts->total() }})
                </a>
                @foreach ($categories as $item)
                    <a href="{{ LocaleUrls::category($item) }}"
                       @class([
                           'border px-4 py-2.5 text-sm transition',
                           'border-navy-900 bg-navy-900 text-cream-bright' => $category?->is($item),
                           'border-line text-ink-muted hover:border-gold-500' => ! $category?->is($item),
                       ])>
                        {{ $item->localized('name') }} ({{ $item->posts_count }})
                    </a>
                @endforeach
            </div>

            @if ($featured)
                <a href="{{ LocaleUrls::post($featured) }}" class="grid gap-8 border-t-2 border-navy-900 pt-8 lg:grid-cols-[1.2fr_1fr] lg:gap-14">
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-wrap justify-between gap-3 text-xs font-semibold tracking-[0.16em] text-gold-700 uppercase">
                            <span>{{ $featured->category?->localized('name') }}</span>
                            <span class="text-mist-dim">{{ $featured->published_at?->translatedFormat('j F Y') }}</span>
                        </div>
                        <h2 class="font-display text-4xl leading-tight font-semibold text-navy-900 lg:text-5xl">{{ $featured->localized('title') }}</h2>
                        @if ($featured->localized('excerpt'))
                            <p class="text-base leading-relaxed text-ink-muted">{{ $featured->localized('excerpt') }}</p>
                        @endif
                        <span class="pt-2 text-sm font-bold text-navy-900">{{ __('front.read_article') }} &rarr;</span>
                    </div>
                    @if ($featured->getFirstMediaUrl('cover'))
                        <img src="{{ $featured->getFirstMediaUrl('cover', 'card') }}" alt="{{ $featured->localized('title') }}" class="h-64 w-full object-cover lg:h-full" loading="lazy">
                    @endif
                </a>
            @endif

            <div class="grid gap-12 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($rest as $post)
                    <a href="{{ LocaleUrls::post($post) }}" class="flex flex-col gap-4 border-t-2 border-navy-900 pt-6">
                        <div class="flex flex-wrap justify-between gap-3 text-xs font-semibold tracking-[0.16em] text-gold-700 uppercase">
                            <span>{{ $post->category?->localized('name') }}</span>
                            <span class="text-mist-dim">{{ $post->published_at?->translatedFormat('j F Y') }}</span>
                        </div>
                        <h3 class="font-display text-3xl leading-tight font-semibold text-navy-900">{{ $post->localized('title') }}</h3>
                        @if ($post->localized('excerpt'))
                            <p class="text-[15px] leading-relaxed text-ink-muted">{{ $post->localized('excerpt') }}</p>
                        @endif
                    </a>
                @endforeach
            </div>

            {{ $posts->links() }}
        </div>
    </section>
</x-layouts.app>
