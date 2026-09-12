<?php

namespace App\Http\Middleware;

use App\Support\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the `{locale}` route prefix into the application locale.
 *
 * Also registers the locale as a default URL parameter so that every
 * `route()` call in views stays inside the visitor's language without
 * having to pass the locale explicitly.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        abort_unless(Locale::isSupported($locale), 404);

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        // Drop the locale from the route's parameters once it has been
        // consumed.
        //
        // Laravel dispatches controller actions with the route parameters
        // positionally. Because `{locale}` is the first segment, a method
        // like `show(Service $service)` would otherwise receive the string
        // "en" as its first argument. URL generation is unaffected: the
        // URL::defaults() call above supplies the locale to route().
        $request->route()->forgetParameter('locale');

        return $next($request);
    }
}
