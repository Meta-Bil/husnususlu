<?php

use App\Http\Controllers\Front\SitemapController;
use App\Http\Middleware\SetLocale;
use App\Support\Localization\Locales;
use Illuminate\Support\Facades\Route;

Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('robots.txt', [SitemapController::class, 'robots'])->name('robots');

/*
 * One route group per enabled locale. Turkish is served without a prefix, the
 * other languages under their own. Route names are prefixed with the locale
 * ("en.blog.show"), so links stay inside the visitor's language.
 */
$locales = Locales::enabled();

/*
 * Prefixed locales are registered first: the default locale has no prefix and
 * ends with a catch-all page route, which would otherwise swallow "/en".
 */
usort($locales, fn (string $a, string $b): int => strlen(Locales::prefix($b)) <=> strlen(Locales::prefix($a)));

foreach ($locales as $locale) {
    Route::prefix(Locales::prefix($locale))
        ->name($locale.'.')
        ->middleware(SetLocale::class.':'.$locale)
        ->group(function () use ($locale): void {
            require base_path('routes/front.php');
        });
}
