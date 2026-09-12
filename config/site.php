<?php

/*
|--------------------------------------------------------------------------
| Site Configuration
|--------------------------------------------------------------------------
|
| Single source of truth for company identity, contact details and the
| navigation. Everything here is placeholder data pending the real brand
| kit; swap the values and the whole site updates.
|
| Content that the client will edit themselves (projects, clients,
| testimonials, posts) does NOT belong here — that moves to the database
| and the admin panel in T0.2 / T0.3.
|
*/

return [

    'name' => env('SITE_NAME', 'Clean Cody'),

    // Shown in the <title> suffix and the Organization schema.
    'legal_name' => env('SITE_LEGAL_NAME', 'Clean Cody'),

    'domain' => env('SITE_DOMAIN', 'cleancody.com'),

    'founded_year' => 2021,

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | The key is the URL prefix and the Laravel locale. `dir` drives the
    | document direction, which every layout and animation reads from.
    |
    | Arabic is deferred. The RTL machinery stays in place — logical CSS
    | properties, direction-aware animations, `lang/ar/*`, the switcher and
    | the font-loading split — so enabling it later is this one entry plus a
    | content pass. Do NOT strip that work out; re-adding it is expensive.
    |
    */

    'locales' => [
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'dir' => 'ltr',
            'hreflang' => 'en',
        ],

        // Uncomment to bring Arabic online (see also vite.config.js).
        // 'ar' => [
        //     'name' => 'Arabic',
        //     'native' => 'العربية',
        //     'dir' => 'rtl',
        //     'hreflang' => 'ar',
        // ],
    ],

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Contact
    |--------------------------------------------------------------------------
    */

    'contact' => [
        'email' => env('SITE_EMAIL', 'hello@cleancody.com'),
        'phone' => env('SITE_PHONE', '+961 76 928 097'),
        // Digits only, no +, no spaces — wa.me requires that format.
        'whatsapp' => env('SITE_WHATSAPP', '96176928097'),
        'booking_url' => env('SITE_BOOKING_URL'),
    ],

    'address' => [
        'street' => env('SITE_STREET', ''),
        'city' => env('SITE_CITY', 'Beirut'),
        'country' => env('SITE_COUNTRY', 'Lebanon'),
        'country_code' => env('SITE_COUNTRY_CODE', 'LB'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Social profiles
    |--------------------------------------------------------------------------
    |
    | Null entries are skipped by the footer, so unfinished profiles can
    | simply be left out rather than linking to a dead page.
    |
    */

    'social' => [
        'linkedin' => env('SOCIAL_LINKEDIN', 'https://linkedin.com/company/example'),
        'github' => env('SOCIAL_GITHUB'),
        'x' => env('SOCIAL_X'),
        'instagram' => env('SOCIAL_INSTAGRAM'),
        'facebook' => env('SOCIAL_FACEBOOK'),
        'youtube' => env('SOCIAL_YOUTUBE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Third-party review profiles
    |--------------------------------------------------------------------------
    |
    | Rendered as trust badges in the footer. Research shows third-party
    | review badges are among the highest-impact trust signals on a B2B site.
    |
    */

    'reviews' => [
        'clutch' => env('REVIEWS_CLUTCH'),
        'goodfirms' => env('REVIEWS_GOODFIRMS'),
        'google' => env('REVIEWS_GOOGLE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    |
    | Drives the navigation mega-menu and the homepage services grid. Each
    | `key` resolves to `lang/{locale}/services.php` for its title and
    | description, so no copy lives here.
    |
    | Moves to the `services` table in T0.2. Keep the keys stable — the
    | migration will seed from this list.
    |
    */

    'services' => [
        ['key' => 'mobile-apps', 'icon' => 'device-mobile'],
        ['key' => 'web-development', 'icon' => 'globe'],
        ['key' => 'dashboards', 'icon' => 'chart-bar'],
        ['key' => 'custom-systems', 'icon' => 'puzzle'],
        ['key' => 'erp', 'icon' => 'building'],
        ['key' => 'ai-solutions', 'icon' => 'sparkles'],
        ['key' => 'backend-cloud', 'icon' => 'server'],
    ],

];
