<?php

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
        Route::view('/', 'pages.home')->name('home');

        // Internal reference for the design system. Not linked publicly and
        // not indexed; remove or gate before launch.
        Route::view('/styleguide', 'pages.styleguide')->name('styleguide');
    });
