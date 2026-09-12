<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // A baseline locale for URL generation. SetLocale overrides this
        // per request, but error pages rendered outside the locale group —
        // a 404 on an unknown root path, say — still need route() to work.
        URL::defaults(['locale' => config('site.fallback_locale')]);
    }
}
