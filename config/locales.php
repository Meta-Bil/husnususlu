<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | Turkish is the primary language of the site and is served without a URL
    | prefix. Every other enabled locale is served under its own prefix.
    |
    */

    'default' => 'tr',

    /*
    |--------------------------------------------------------------------------
    | Fallback Chain
    |--------------------------------------------------------------------------
    |
    | When a translatable field is empty in the requested locale, the value is
    | taken from the first locale in this chain that has content.
    |
    */

    'fallback_chain' => ['en', 'tr'],

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | A disabled locale keeps its routes, fonts and right-to-left support ready
    | but is not served to visitors and is left out of menus, the language
    | switcher, hreflang tags and sitemaps until it is enabled.
    |
    */

    'locales' => [
        'tr' => [
            'native' => 'Türkçe',
            'short' => 'TR',
            'dir' => 'ltr',
            'hreflang' => 'tr',
            'prefix' => '',
            'enabled' => true,
        ],
        'en' => [
            'native' => 'English',
            'short' => 'EN',
            'dir' => 'ltr',
            'hreflang' => 'en',
            'prefix' => 'en',
            'enabled' => true,
        ],
        'ru' => [
            'native' => 'Русский',
            'short' => 'RU',
            'dir' => 'ltr',
            'hreflang' => 'ru',
            'prefix' => 'ru',
            'enabled' => true,
        ],
        'ar' => [
            'native' => 'العربية',
            'short' => 'AR',
            'dir' => 'rtl',
            'hreflang' => 'ar',
            'prefix' => 'ar',
            'enabled' => (bool) env('LOCALE_AR_ENABLED', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Translated Route Segments
    |--------------------------------------------------------------------------
    |
    | Fixed URL segments per locale. Content slugs live in the database; these
    | are only the section prefixes that group them.
    |
    */

    'segments' => [
        'pain_types' => [
            'tr' => 'agri-turleri',
            'en' => 'types-of-pain',
            'ru' => 'vidy-boli',
            'ar' => 'anwa-al-alam',
        ],
        'procedures' => [
            'tr' => 'girisimsel-tedaviler',
            'en' => 'interventional-treatments',
            'ru' => 'intervencionnoe-lechenie',
            'ar' => 'al-ilaj-al-tadakhuli',
        ],
        'blog' => [
            'tr' => 'blog',
            'en' => 'blog',
            'ru' => 'blog',
            'ar' => 'blog',
        ],
        'blog_category' => [
            'tr' => 'kategori',
            'en' => 'category',
            'ru' => 'kategoriya',
            'ar' => 'category',
        ],
        'videos' => [
            'tr' => 'video-galeri',
            'en' => 'videos',
            'ru' => 'video',
            'ar' => 'videos',
        ],
    ],

];
