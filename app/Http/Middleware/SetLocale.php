<?php

namespace App\Http\Middleware;

use App\Support\Localization\Locales;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Each locale has its own route group, so the locale is a route parameter
     * of the middleware rather than something guessed from the request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $locale): Response
    {
        abort_unless(Locales::isEnabled($locale), 404);

        app()->setLocale($locale);
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);

        View::share('locale', $locale);
        View::share('direction', Locales::direction($locale));

        return $next($request);
    }
}
