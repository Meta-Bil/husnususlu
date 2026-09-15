{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach ($entries as $entry)
    <url>
        <loc>{{ $entry['url'] }}</loc>
@foreach ($entry['alternates'] as $alternateLocale => $alternateUrl)
        <xhtml:link rel="alternate" hreflang="{{ \App\Support\Localization\Locales::hreflang($alternateLocale) }}" href="{{ $alternateUrl }}"/>
@endforeach
@if ($entry['lastmod'])
        <lastmod>{{ $entry['lastmod'] }}</lastmod>
@endif
    </url>
@endforeach
</urlset>
