<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\CareersController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ScopeController;
use App\Http\Controllers\ServiceController;
use App\Http\Middleware\SetLocale;
use App\Support\Locale;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Every public page lives under a locale prefix (/en, /ar). The bare root
| redirects to whichever language the visitor's browser asks for.
|
*/

Route::get('/', fn () => redirect('/'.Locale::preferred()));

Route::prefix('{locale}')
    ->whereIn('locale', array_keys(config('site.locales')))
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::get('/', HomeController::class)->name('home');

        // Internal reference for the design system. Not linked publicly and
        // not indexed; remove or gate before launch.
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

        Route::get('/work', [ProjectController::class, 'index'])->name('work.index');
        Route::get('/work/{project}', [ProjectController::class, 'show'])->name('work.show');

        Route::get('/about', AboutController::class)->name('about');

        // Registered only while hiring is switched on, so the link never
        // appears in the navigation or the sitemap when it should not.
        if (config('site.careers.enabled')) {
            Route::get('/careers', [CareersController::class, 'show'])->name('careers');
            Route::post('/careers', [CareersController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('careers.store');
        }

        Route::get('/contact', [ContactController::class, 'show'])->name('contact');
        Route::get('/contact/thank-you', [ContactController::class, 'thanks'])->name('contact.thanks');

        // Rate limited per IP. Lead forms are the only public write path on
        // the site, so they are the only thing worth flooding.
        Route::post('/contact', [ContactController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('contact.store');

        Route::post('/newsletter', [NewsletterController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('newsletter.store');

        Route::get('/start-a-project', [ScopeController::class, 'show'])->name('scope');
        Route::post('/start-a-project', [ScopeController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('scope.store');

        Route::view('/styleguide', 'pages.styleguide')->name('styleguide');
    });
