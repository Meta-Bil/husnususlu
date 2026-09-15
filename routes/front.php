<?php

/**
 * Public routes, registered once per locale from routes/web.php.
 *
 * @var string $locale
 */

use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\TreatmentController;
use App\Http\Controllers\Front\VideoController;
use App\Support\Localization\Locales;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

$painTypes = Locales::segment('pain_types', $locale);
Route::get($painTypes, [TreatmentController::class, 'painTypeIndex'])->name('pain-types.index');
Route::get($painTypes.'/{slug}', [TreatmentController::class, 'painType'])->name('pain-types.show');

$procedures = Locales::segment('procedures', $locale);
Route::get($procedures, [TreatmentController::class, 'procedureIndex'])->name('procedures.index');
Route::get($procedures.'/{slug}', [TreatmentController::class, 'procedure'])->name('procedures.show');

$blog = Locales::segment('blog', $locale);
Route::get($blog, [BlogController::class, 'index'])->name('blog.index');
Route::get($blog.'/'.Locales::segment('blog_category', $locale).'/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get($blog.'/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get(Locales::segment('videos', $locale), VideoController::class)->name('videos');

/*
 * Editorial pages live at the root of their locale. Registered last so it never
 * shadows a section above, and constrained so it cannot swallow the admin panel
 * or framework routes.
 */
Route::get('{slug}', [PageController::class, 'show'])
    ->where('slug', '(?!admin|livewire|storage|build|up$)[\p{L}\p{N}\-_%]+')
    ->name('page');
