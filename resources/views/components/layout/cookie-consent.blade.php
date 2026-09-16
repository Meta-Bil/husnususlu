@php
    $settings = app(\App\Settings\SiteSettings::class);
    $hasMeasurement = filled($settings->gtm_id) || filled($settings->ga_measurement_id);
@endphp

@if ($hasMeasurement)
    <div id="cerez-onayi"
         hidden
         class="fixed inset-x-4 bottom-4 z-50 mx-auto max-w-3xl border border-gold-500/40 bg-navy-900 p-5 text-cream shadow-[0_18px_40px_rgba(0,0,0,0.4)] sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between sm:gap-8">
            <p class="text-sm leading-relaxed text-mist">{{ __('front.cookie_text') }}</p>
            <div class="flex shrink-0 gap-3">
                <button type="button" data-cookie-choice="reddet"
                        class="h-11 border border-cream-bright/35 px-4 text-sm font-semibold text-cream-bright hover:border-gold-500 hover:text-gold-300">
                    {{ __('front.cookie_decline') }}
                </button>
                <button type="button" data-cookie-choice="kabul"
                        class="h-11 bg-gold-500 px-5 text-sm font-bold text-navy-900 hover:bg-gold-400">
                    {{ __('front.cookie_accept') }}
                </button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var KEY = 'cerez-onayi';
            var banner = document.getElementById('cerez-onayi');
            var choice = null;

            try { choice = localStorage.getItem(KEY); } catch (e) { choice = null; }

            if (choice === 'kabul') {
                loadMeasurement();
            } else if (choice !== 'reddet' && banner) {
                banner.hidden = false;
            }

            if (banner) {
                banner.addEventListener('click', function (event) {
                    var button = event.target.closest('[data-cookie-choice]');
                    if (!button) { return; }
                    var value = button.getAttribute('data-cookie-choice');
                    try { localStorage.setItem(KEY, value); } catch (e) {}
                    banner.hidden = true;
                    if (value === 'kabul') { loadMeasurement(); }
                });
            }

            function loadMeasurement() {
                @if (filled($settings->gtm_id))
                    window.dataLayer = window.dataLayer || [];
                    window.dataLayer.push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
                    var gtm = document.createElement('script');
                    gtm.async = true;
                    gtm.src = 'https://www.googletagmanager.com/gtm.js?id={{ $settings->gtm_id }}';
                    document.head.appendChild(gtm);
                @elseif (filled($settings->ga_measurement_id))
                    var ga = document.createElement('script');
                    ga.async = true;
                    ga.src = 'https://www.googletagmanager.com/gtag/js?id={{ $settings->ga_measurement_id }}';
                    document.head.appendChild(ga);
                    window.dataLayer = window.dataLayer || [];
                    function gtag() { window.dataLayer.push(arguments); }
                    gtag('js', new Date());
                    gtag('config', '{{ $settings->ga_measurement_id }}');
                @endif
            }
        })();
    </script>
@endif
