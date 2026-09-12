<?php

namespace App\Support;

use App\Models\Service;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class Nav
{
    /**
     * Resolve a named route that may not exist yet.
     *
     * Pages arrive across several epics. Until a route is defined the link
     * falls back to something useful — an anchor or a mailto — rather than
     * rendering a dead button, and starts working the moment the route is
     * registered.
     *
     * @param  array<string, mixed>|string  $parameters
     */
    public static function link(string $name, string $fallback = '#', array|string $parameters = []): string
    {
        if (! Route::has($name)) {
            return $fallback;
        }

        try {
            return route($name, $parameters);
        } catch (UrlGenerationException) {
            // The route exists but required parameters were not supplied —
            // a nav link to a detail page, for instance. Fall back rather
            // than take the page down.
            return $fallback;
        }
    }

    /**
     * Published services for the navigation and footer.
     *
     * Read from the database rather than config so unpublishing a service
     * removes it from the menu too. Memoised because the header and footer
     * both need it on every page.
     *
     * @return Collection<int, Service>
     */
    public static function services(): Collection
    {
        return once(fn () => Service::published()->ordered()->get());
    }

    /** The best available way for a visitor to start a conversation. */
    public static function contact(): string
    {
        return static::link('contact', 'mailto:'.config('site.contact.email'));
    }
}
