@php
    use App\Support\Localization\LocaleUrls;
    use App\Support\Localization\Locales;
    use App\Support\Navigation\Navigation;

    $settings = app(\App\Settings\SiteSettings::class);
    $locale = app()->getLocale();
    $items = Navigation::menu('header', $locale);
    $languages = LocaleUrls::switcher($page ?? null, 'home');
@endphp

<header class="bg-navy-900 text-cream-bright">
    <div class="hidden border-b border-gold-400/20 lg:block">
        <div class="mx-auto flex h-10 max-w-[1200px] items-center justify-between text-[12.5px] text-mist-muted">
            <div class="flex items-center gap-7">
                <span>{{ $settings->address[$locale] ?? $settings->address['tr'] ?? '' }}</span>
                <span>{{ $settings->working_hours[$locale] ?? $settings->working_hours['tr'] ?? '' }}</span>
            </div>
            <div class="flex items-center gap-7">
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $settings->clinic_phone) }}" class="text-cream">{{ $settings->clinic_phone }}</a>
                <div class="flex items-center gap-3.5 font-semibold tracking-wider">
                    @foreach ($languages as $code => $url)
                        <a href="{{ $url }}" @class(['text-gold-400' => $code === $locale, 'text-mist-muted hover:text-cream' => $code !== $locale])>
                            {{ Locales::short($code) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto flex h-16 max-w-[1200px] items-center justify-between gap-6 px-6 lg:h-23 lg:px-0">
        <a href="{{ route($locale.'.home') }}" class="flex flex-col gap-1">
            <span class="font-display text-xl leading-none font-semibold text-cream-bright lg:text-[28px]">{{ config('app.name') }}</span>
            <span class="text-[9px] font-semibold tracking-[0.32em] text-gold-500 uppercase lg:text-[10.5px]">{{ __('front.doctor_title') }}</span>
        </a>

        <nav class="hidden items-center gap-7 text-sm font-medium text-[#d9dde6] xl:flex" aria-label="{{ __('front.menu') }}">
            @foreach ($items as $item)
                @if (filled($item['children']))
                    <div class="group relative">
                        <a href="{{ $item['url'] ?? '#' }}" class="flex items-center gap-1.5 py-8 hover:text-gold-300">
                            {{ $item['label'] }}
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                        </a>
                        <div class="invisible absolute top-full start-0 z-20 w-72 border border-gold-400/25 bg-navy-800 py-3 opacity-0 transition group-hover:visible group-hover:opacity-100">
                            @foreach ($item['children'] as $child)
                                <a href="{{ $child['url'] ?? '#' }}" class="block px-5 py-2.5 text-sm text-mist hover:bg-navy-900 hover:text-gold-300">{{ $child['label'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $item['url'] ?? '#' }}" class="py-8 hover:text-gold-300">{{ $item['label'] }}</a>
                @endif
            @endforeach
        </nav>

        <div class="flex items-center gap-4">
            <a href="{{ route($locale.'.home') }}#randevu" class="hidden h-11 items-center border border-gold-500 px-5.5 text-[13px] font-semibold tracking-wider text-gold-300 uppercase hover:bg-gold-500 hover:text-navy-900 lg:flex">
                {{ __('front.request_appointment') }}
            </a>

            <details class="xl:hidden">
                <summary class="flex size-11 cursor-pointer list-none items-center justify-center text-cream-bright">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M10 17h10"/></svg>
                    <span class="sr-only">{{ __('front.menu') }}</span>
                </summary>
                <div class="absolute inset-x-0 z-30 border-t border-gold-400/20 bg-navy-800 px-6 py-6">
                    <nav class="flex flex-col gap-4 text-cream" aria-label="{{ __('front.menu') }}">
                        @foreach ($items as $item)
                            <a href="{{ $item['url'] ?? '#' }}" class="text-base font-medium">{{ $item['label'] }}</a>
                            @foreach ($item['children'] as $child)
                                <a href="{{ $child['url'] ?? '#' }}" class="ps-4 text-sm text-mist">{{ $child['label'] }}</a>
                            @endforeach
                        @endforeach
                    </nav>
                    <div class="mt-6 flex gap-4 border-t border-gold-400/20 pt-4 text-sm font-semibold">
                        @foreach ($languages as $code => $url)
                            <a href="{{ $url }}" @class(['text-gold-400' => $code === $locale, 'text-mist-muted' => $code !== $locale])>{{ Locales::short($code) }}</a>
                        @endforeach
                    </div>
                </div>
            </details>
        </div>
    </div>
</header>
