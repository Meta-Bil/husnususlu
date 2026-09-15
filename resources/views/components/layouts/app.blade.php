@props([
    'title' => null,
    'description' => null,
    'canonical' => null,
    'alternates' => [],
    'noindex' => false,
    'jsonLd' => [],
])

@php
    use App\Support\Localization\Locales;

    $locale = app()->getLocale();
    $direction = Locales::direction($locale);
    $settings = app(\App\Settings\SiteSettings::class);
@endphp
<!DOCTYPE html>
<html lang="{{ Locales::hreflang($locale) }}" dir="{{ $direction }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <title>{{ $title ?? config('app.name') }}</title>

    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif

    @if ($noindex)
        <meta name="robots" content="noindex, follow">
    @endif

    @if ($canonical)
        <link rel="canonical" href="{{ $canonical }}">
    @endif

    @foreach ($alternates as $alternateLocale => $alternateUrl)
        <link rel="alternate" hreflang="{{ Locales::hreflang($alternateLocale) }}" href="{{ $alternateUrl }}">
    @endforeach

    @if (filled($alternates))
        <link rel="alternate" hreflang="x-default" href="{{ $alternates[Locales::default()] ?? reset($alternates) }}">
    @endif

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    @if ($description)
        <meta property="og:description" content="{{ $description }}">
    @endif
    @if ($canonical)
        <meta property="og:url" content="{{ $canonical }}">
    @endif

    @if (filled($jsonLd))
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="bg-paper font-sans text-ink antialiased">
    <a href="#icerik" class="sr-only focus:not-sr-only focus:absolute focus:start-4 focus:top-4 focus:z-50 focus:bg-gold-500 focus:px-4 focus:py-2 focus:text-navy-900">
        {{ __('front.contents') }}
    </a>

    <x-layout.header />

    <main id="icerik">
        {{ $slot }}
    </main>

    <x-layout.footer />
    <x-layout.whatsapp-button />

    @stack('scripts')
</body>
</html>
