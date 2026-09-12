<?php

namespace App\Support;

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
     */
    public static function link(string $name, string $fallback = '#'): string
    {
        return Route::has($name) ? route($name) : $fallback;
    }

    /** The best available way for a visitor to start a conversation. */
    public static function contact(): string
    {
        return static::link('contact', 'mailto:'.config('site.contact.email'));
    }
}
