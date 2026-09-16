@php
    use App\Support\Localization\Locales;

    $locale = Locales::isEnabled(app()->getLocale()) ? app()->getLocale() : Locales::default();
@endphp

<x-layouts.app :title="__('front.not_found_title')" :noindex="true">
    <section class="bg-navy-900 text-cream-bright">
        <div class="mx-auto flex max-w-[1200px] flex-col items-start gap-7 px-6 py-24 lg:px-0 lg:py-32">
            <p class="text-xs font-semibold tracking-[0.3em] text-gold-500 uppercase">404</p>
            <h1 class="font-display text-5xl leading-tight font-medium lg:text-7xl">{{ __('front.not_found_title') }}</h1>
            <p class="max-w-xl text-lg leading-relaxed text-mist">{{ __('front.not_found_text') }}</p>
            <div class="flex flex-wrap gap-4 pt-2">
                <x-ui.button :href="route($locale.'.home')" variant="gold" icon="arrow">{{ __('front.home') }}</x-ui.button>
                <x-ui.button :href="route($locale.'.blog.index')" variant="outline">{{ __('front.all_posts') }}</x-ui.button>
            </div>
        </div>
    </section>
</x-layouts.app>
