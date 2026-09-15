@php
    use App\Support\Localization\Locales;
    use App\Support\Localization\LocaleUrls;
    use App\Support\Navigation\Navigation;

    $settings = app(\App\Settings\SiteSettings::class);
    $locale = app()->getLocale();
    $treatments = Navigation::menu('footer_treatments', $locale);
    $corporate = Navigation::menu('footer_corporate', $locale);
    $languages = LocaleUrls::switcher(null, 'home');
@endphp

<footer class="bg-navy-950 text-mist-muted">
    <div class="mx-auto flex max-w-[1200px] flex-col gap-12 px-6 py-16 lg:px-0">
        <div class="grid gap-12 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr]">
            <div class="flex flex-col gap-4">
                <span class="font-display text-[28px] font-semibold text-cream-bright">{{ config('app.name') }}</span>
                <span class="text-[10.5px] font-semibold tracking-[0.32em] text-gold-500 uppercase">{{ __('front.doctor_title') }}</span>
                <div class="flex gap-3.5 pt-2">
                    @if ($url = $settings->socials['instagram'] ?? null)
                        <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="Instagram" class="hover:text-gold-300">
                            <svg class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
                        </a>
                    @endif
                    @if ($url = $settings->socials['youtube'] ?? null)
                        <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="YouTube" class="hover:text-gold-300">
                            <svg class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="m10 9.5 5 2.5-5 2.5z" fill="currentColor"/></svg>
                        </a>
                    @endif
                    @if ($url = $settings->socials['facebook'] ?? null)
                        <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="Facebook" class="hover:text-gold-300">
                            <svg class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"><path d="M14 8h3V4h-3a4 4 0 0 0-4 4v2H7v4h3v6h4v-6h3l1-4h-4V8z"/></svg>
                        </a>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-3 text-[14.5px]">
                <span class="pb-1.5 text-[11.5px] font-semibold tracking-[0.24em] text-gold-500 uppercase">{{ __('front.treatments') }}</span>
                @foreach ($treatments as $item)
                    <a href="{{ $item['url'] ?? '#' }}" class="hover:text-gold-300">{{ $item['label'] }}</a>
                @endforeach
            </div>

            <div class="flex flex-col gap-3 text-[14.5px]">
                <span class="pb-1.5 text-[11.5px] font-semibold tracking-[0.24em] text-gold-500 uppercase">{{ __('front.corporate') }}</span>
                @foreach ($corporate as $item)
                    <a href="{{ $item['url'] ?? '#' }}" @if ($item['target'] === '_blank') target="_blank" rel="noopener" @endif class="hover:text-gold-300">{{ $item['label'] }}</a>
                @endforeach
            </div>

            <div class="flex flex-col gap-3 text-[14.5px] leading-relaxed">
                <span class="pb-1.5 text-[11.5px] font-semibold tracking-[0.24em] text-gold-500 uppercase">{{ __('front.contact') }}</span>
                <span>{{ $settings->address[$locale] ?? $settings->address['tr'] ?? '' }}</span>
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $settings->clinic_phone) }}" class="hover:text-gold-300">{{ $settings->clinic_phone }}</a>
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $settings->whatsapp_phone) }}" class="hover:text-gold-300">{{ $settings->whatsapp_phone }}</a>
                <a href="mailto:{{ $settings->email }}" class="hover:text-gold-300">{{ $settings->email }}</a>
                <span>{{ $settings->working_hours[$locale] ?? $settings->working_hours['tr'] ?? '' }}</span>
            </div>
        </div>

        <div class="flex flex-col gap-4 border-t border-gold-400/20 pt-7 text-[13px] text-[#6f7890] lg:flex-row lg:items-center lg:justify-between">
            <span>{{ $settings->medical_disclaimer[$locale] ?? $settings->medical_disclaimer['tr'] ?? '' }}</span>
            <div class="flex flex-wrap items-center gap-6">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
                <div class="flex gap-3 tracking-wider">
                    @foreach ($languages as $code => $url)
                        <a href="{{ $url }}" @class(['text-gold-400' => $code === $locale, 'hover:text-cream' => $code !== $locale])>{{ Locales::short($code) }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</footer>
