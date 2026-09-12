<?php

namespace App\Support;

use App\Models\Post;
use App\Models\Service;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
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
        } catch (UrlGenerationException $exception) {
            // The route exists but required parameters were missing. That
            // is a programming mistake, not a page that has not shipped
            // yet, and silently falling back hides it: every service and
            // case study card on the homepage once pointed at an anchor
            // because of exactly this. Fail loudly in development; in
            // production degrade rather than take the page down, but leave
            // a trace.
            if (config('app.debug')) {
                throw $exception;
            }

            Log::warning('Nav::link fell back on a missing route parameter', [
                'route' => $name,
                'parameters' => $parameters,
            ]);

            return $fallback;
        }
    }

    /**
     * Published services for the navigation and footer.
     *
     * Read from the database rather than config so unpublishing a service
     * removes it from the menu too.
     *
     * Memoised per request — the header, the footer and often the page
     * itself all need the same rows.
     *
     * @return Collection<int, Service>
     */
    public static function services(): Collection
    {
        return PerRequest::remember('nav.services', fn () => Service::published()->ordered()->get());
    }

    /**
     * Whether anything is published on the blog.
     *
     * The Insights link is hidden until there is something behind it —
     * sending a visitor to an empty page costs more than a missing link.
     */
    public static function hasPosts(): bool
    {
        return PerRequest::remember('nav.has-posts', fn () => Post::published()->exists());
    }

    /** The best available way for a visitor to start a conversation. */
    public static function contact(): string
    {
        return static::link('contact', 'mailto:'.config('site.contact.email'));
    }

    /**
     * The highest-intent entry point.
     *
     * "Start a project" buttons go to the scoping wizard, which captures
     * far more than a bare contact form, and fall back to contact.
     */
    public static function start(): string
    {
        return static::link('scope', static::contact());
    }
}
