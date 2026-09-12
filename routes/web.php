<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\CareersController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ScopeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
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

// Outside the locale group: both are single documents covering every locale.
Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::prefix('{locale}')
    // Every locale the project supports, not just the ones currently live.
    // Route patterns are compiled at boot, so deriving this from config
    // would mean a route cache had to be rebuilt to switch a language on.
    // SetLocale is the authority on what is actually enabled and 404s the
    // rest, which keeps config/site.php the single source of truth.
    ->whereIn('locale', ['en', 'ar'])
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

        // Required rather than optional: the site collects names, email
        // addresses, phone numbers and IP addresses through its forms.
        Route::get('/privacy', fn () => app(LegalController::class)('privacy'))->name('privacy');
        Route::get('/terms', fn () => app(LegalController::class)('terms'))->name('terms');

        // Registered only while hiring is switched on, so the link never
        // appears in the navigation or the sitemap when it should not.
        if (config('site.careers.enabled')) {
            Route::get('/careers', [CareersController::class, 'show'])->name('careers');
            Route::post('/careers', [CareersController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('careers.store');
        }

        Route::get('/insights', [PostController::class, 'index'])->name('posts.index');
        Route::get('/insights/{post}', [PostController::class, 'show'])->name('posts.show');

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
