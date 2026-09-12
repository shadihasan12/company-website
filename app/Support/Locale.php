<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Locale helpers shared by the layout, the language switcher and (later)
 * the hreflang tags.
 */
class Locale
{
    /**
     * All configured locales, keyed by code.
     *
     * @return array<string, array{name: string, native: string, dir: string, hreflang: string}>
     */
    public static function all(): array
    {
        return config('site.locales');
    }

    public static function isSupported(?string $locale): bool
    {
        return $locale !== null && array_key_exists($locale, static::all());
    }

    public static function current(): string
    {
        return app()->getLocale();
    }

    public static function direction(?string $locale = null): string
    {
        return static::all()[$locale ?? static::current()]['dir'] ?? 'ltr';
    }

    public static function isRtl(?string $locale = null): bool
    {
        return static::direction($locale) === 'rtl';
    }

    /**
     * The current URL expressed in another locale.
     *
     * Rebuilds from the matched route rather than string-replacing the path,
     * so query strings and route parameters survive the switch. Falls back to
     * the locale home page for anything not matched by a named route.
     */
    public static function urlFor(string $locale): string
    {
        $route = Route::current();
        $name = $route?->getName();

        if ($name === null) {
            return url("/{$locale}");
        }

        // Route::view() and ->defaults() inject non-URI values such as `view`
        // and `status` into parameters(). Passing those to route() would leak
        // them into the query string, so keep only real URI segments.
        $parameters = array_intersect_key(
            $route->parameters(),
            array_flip($route->parameterNames()),
        );

        // Preserve the query string so switching language does not discard
        // filters, pagination or campaign parameters.
        return route($name, [
            ...$parameters,
            ...request()->query(),
            'locale' => $locale,
        ]);
    }

    /**
     * Every locale except the current one, ready for the switcher.
     *
     * @return array<int, array{code: string, native: string, dir: string, url: string}>
     */
    public static function alternates(): array
    {
        return collect(static::all())
            ->except(static::current())
            ->map(fn (array $meta, string $code) => [
                'code' => $code,
                'native' => $meta['native'],
                'dir' => $meta['dir'],
                'url' => static::urlFor($code),
            ])
            ->values()
            ->all();
    }

    /**
     * The locale a visitor most likely wants, from their browser settings.
     */
    public static function preferred(): string
    {
        return request()->getPreferredLanguage(array_keys(static::all()))
            ?? config('site.fallback_locale');
    }
}
