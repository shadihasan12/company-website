<?php

use App\Http\Controllers\HomeController;
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

        Route::view('/styleguide', 'pages.styleguide')->name('styleguide');
    });
