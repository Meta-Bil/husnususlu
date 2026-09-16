<?php

use App\Enums\PageTemplate;
use App\Enums\TreatmentKind;

return [

    /*
    |--------------------------------------------------------------------------
    | Source
    |--------------------------------------------------------------------------
    |
    | The old WordPress install. The database connection is declared in
    | `config/database.php` and is only ever read from.
    |
    */

    'connection' => 'wp',

    'uploads_path' => env('WP_UPLOADS_PATH', 'C:\laragon\www\husnususlu\wp-content\uploads'),

    'source_url' => env('WP_SOURCE_URL', 'https://husnususlu.test'),

    'site_name' => 'Prof. Dr. Hüsnü Süslü',

    /*
    | WordPress language codes mapped onto the locales of the new site. Any
    | other language on the old site is left behind.
    */

    'locales' => [
        'tr' => 'tr',
        'en' => 'en',
    ],

    /*
    |--------------------------------------------------------------------------
    | What Becomes What
    |--------------------------------------------------------------------------
    |
    | The old site kept pain types, procedures and editorial pages all as
    | WordPress pages. These lists say which Turkish page becomes which record;
    | the English twin is found through its WPML `trid` and merged in.
    |
    */

    'treatments' => [
        TreatmentKind::PainType->value => [299, 360, 374, 383, 394, 407, 416, 431, 441, 451],
        TreatmentKind::Procedure->value => [465, 474, 480, 492, 499, 505, 511, 517, 525, 1701, 2116],
    ],

    'pages' => [
        50 => PageTemplate::Home->value,
        802 => PageTemplate::Default->value,
        900 => PageTemplate::Contact->value,
        1140 => PageTemplate::Contact->value,
        1076 => PageTemplate::Default->value,
        2 => PageTemplate::Default->value,
        1448 => PageTemplate::Landing->value,
        1197 => PageTemplate::Default->value,
        3 => PageTemplate::Default->value,
        752 => PageTemplate::Videos->value,
        2187 => PageTemplate::BlogIndex->value,
        1127 => PageTemplate::Default->value,
        1132 => PageTemplate::Default->value,
        1959 => PageTemplate::Default->value,
    ],

    /*
    | Published pages that are deliberately left behind. Their old URLs still
    | get a redirect, to the path named here.
    |
    | The English "Ozone Therapy" page (1354) is not listed: the blog post it
    | duplicated has the same slug and claims that address by itself.
    */

    'retired_pages' => [
        2258 => '/hakkimda',
        1978 => '/iletisim',
    ],

    /*
    |--------------------------------------------------------------------------
    | Videos
    |--------------------------------------------------------------------------
    */

    'videos' => [
        'source_page' => 752,

        /* A title mentioning any of these came from a television appearance. */
        'tv_keywords' => ['NTV', 'Beyaz', 'TV100', 'Konuştukça', 'Canlı Yayın'],

        'thumbnail_urls' => [
            'https://i.ytimg.com/vi/:id/maxresdefault.jpg',
            'https://i.ytimg.com/vi/:id/hqdefault.jpg',
        ],

        'thumbnail_timeout' => 15,
    ],

    /*
    | Removed from the front of a video title, longest first.
    */

    'title_prefixes' => [
        'Profesör Doktor Hüsnü Süslü',
        'Doç.Dr. Hüsnü Süslü',
        'Doç. Dr. Hüsnü Süslü',
        'Prof. Dr. Hüsnü Süslü',
        'Prof.Dr. Hüsnü Süslü',
        'Op. Dr. Hüsnü Süslü',
        'Dr. Hüsnü Süslü',
        'Hüsnü Süslü',
    ],

    'title_noise' => ['#shorts', '#short'],

    /*
    |--------------------------------------------------------------------------
    | Menus
    |--------------------------------------------------------------------------
    |
    | The header is the Turkish `ana-menu` with the English `main-menu` merged
    | onto it through WPML. The two footer menus did not exist on the old site
    | and are built from the records the import creates.
    |
    */

    'menus' => [
        'header' => [
            'name' => 'Üst menü',
            'source' => 'ana-menu',
            'merge' => 'main-menu',
        ],
        'footer_treatments' => [
            'name' => 'Alt menü — tedaviler',
        ],
        'footer_corporate' => [
            'name' => 'Alt menü — kurumsal',
            /* WordPress page ids, in the order they should appear. */
            'pages' => [802, 900, 2187, 752, 1197, 3],
        ],
    ],

    /*
    | Menu items pointing at one of these URLs are the WPML language switcher,
    | which the new site renders by itself.
    */

    'menu_item_url_blocklist' => ['#hs-dil', '#'],

    /*
    |--------------------------------------------------------------------------
    | Site Chrome
    |--------------------------------------------------------------------------
    |
    | Elementor kept the header, footer and call-to-action rails inside every
    | page. These widgets are never content.
    |
    */

    'chrome' => [
        'widgets' => [
            'animated-headline',
            'author-box',
            'breadcrumbs',
            'button',
            'divider',
            'google_maps',
            'icon',
            'image-carousel',
            'link-in-bio',
            'lottie',
            'nav-menu',
            'search-form',
            'share-buttons',
            'shortcode',
            'sidebar',
            'social-icons',
            'table-of-contents',
            'theme-post-title',
            'theme-site-logo',
        ],

        /* A text seen on this many source documents is furniture, not content. */
        'fingerprint_threshold' => 4,

        /* Widget types the fingerprint rule may drop. Cards and media are
           matched against records instead, so they are left alone. */
        'fingerprint_widgets' => ['text-editor', 'heading', 'html', 'icon-list'],

        /* An icon list whose items only link to these is a contact or social rail. */
        'contact_link_patterns' => [
            'tel:', 'mailto:', 'wa.me', 'whatsapp', 'maps.google', 'goo.gl', 'maps.app',
            'instagram.com', 'facebook.com', 'twitter.com', 'x.com', 'linkedin.com',
            'youtube.com', 't.me', 'doktortakvimi',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Rules
    |--------------------------------------------------------------------------
    |
    | Turkish health advertising rules forbid guarantees, superlatives and
    | success rates. Anything matching is removed and written into the record's
    | import notes so an editor can see what went.
    |
    */

    'promotional_claims' => [
        '/%\s*\d+(?:[.,]\d+)?\s*(?:-\s*\d+)?\s*(?:oranında\s+)?başarı/u',
        '/(?:yüksek\s+)?başarı\s+oran\w*\s*[:(]?\s*%?\s*\d+/u',
        '/ağrısız\s*[-–—]?\s*acısız/u',
        '/acısız\s*[-–—]?\s*ağrısız/u',
        '/türkiye[\'’`]?\s*n[ıi]n\s+önde\s+gelen/u',
        '/en\s+iyi\s+(?:doktor|hekim|klinik|tedavi|yöntem|uzman)\w*/u',
        '/kesin\s+çözüm/u',
        '/kesin\s+sonuç\s+garanti\w*/u',
        '/(?:sonuç|başarı|iyileşme)\s+garanti\w*/u',
    ],

    /*
    | Typing mistakes in the source that would otherwise be carried over.
    */

    'typos' => [
        'Steroz' => 'Stenoz',
        'steroz' => 'stenoz',
        'STEROZ' => 'STENOZ',
        'Gögüs' => 'Göğüs',
        'GÖGÜS' => 'GÖĞÜS',
        'AGRILARI' => 'AĞRILARI',
        'AGRI' => 'AĞRI',
        'Whatsaap' => 'WhatsApp',
        'Polikliğini' => 'Polikliniği',
    ],

    /*
    | Numbers the old site got wrong. The counter widgets are imported as they
    | stand except for these.
    */

    'corrections' => [
        'publications' => 28,
    ],

    /*
    |--------------------------------------------------------------------------
    | Sanitizer
    |--------------------------------------------------------------------------
    |
    | Post bodies come out of Elementor as arbitrary HTML. Only these elements
    | survive, and every inline style goes.
    |
    */

    'sanitizer' => [
        'elements' => [
            'p' => [],
            'h2' => [],
            'h3' => [],
            'h4' => [],
            'ul' => [],
            'ol' => [],
            'li' => [],
            'strong' => [],
            'em' => [],
            'a' => ['href'],
            'img' => ['src', 'alt'],
            'blockquote' => [],
            'table' => [],
            'thead' => [],
            'tbody' => [],
            'tr' => [],
            'th' => [],
            'td' => [],
            'br' => [],
        ],

        /* Removed together with everything inside them. */
        'dropped_elements' => ['script', 'style', 'iframe', 'noscript', 'svg', 'form', 'button', 'select', 'textarea'],

        'max_input_length' => 500_000,
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    'seo' => [
        'replacements' => [
            '%%sep%%' => '–',
            '%%sitename%%' => 'Prof. Dr. Hüsnü Süslü',
            '%%page%%' => '',
            '%%primary_category%%' => '',
        ],
    ],

];
