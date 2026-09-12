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
    | Credibility statistics
    |--------------------------------------------------------------------------
    |
    | The band under the case studies. Every top agency site runs one:
    | Netguru's reads "NPS 73 · 2500+ projects · 400+ people · 17+ years".
    |
    | Anything left null is hidden rather than guessed, so the band never
    | shows a number nobody can stand behind. Projects, years and industries
    | are computed from real data and need nothing here.
    |
    | Headcount is deliberately absent — the site references neither the
    | team nor its size.
    |
    */

    'stats' => [
        'clients' => null,      // e.g. 24
        'app_rating' => null,   // e.g. 4.8
        'uptime' => null,       // e.g. 99.9
        'nps' => null,          // e.g. 73
    ],

    /*
    |--------------------------------------------------------------------------
    | Lead capture
    |--------------------------------------------------------------------------
    |
    | `budget_ranges` and `timelines` are the options offered in the contact
    | form and the scoping wizard. Keys are stored on the lead; labels live
    | in lang/{locale}/contact.php.
    |
    | `notify` is where new leads are emailed. Defaults to the public
    | address so a fresh install still reaches somebody.
    |
    */

    'leads' => [
        'notify' => env('LEADS_NOTIFY_EMAIL', env('SITE_EMAIL', 'hello@cleancody.com')),

        'budget_ranges' => ['under-10k', '10k-25k', '25k-60k', 'over-60k', 'unsure'],

        'timelines' => ['asap', '1-3-months', '3-6-months', 'exploring'],

        // Minimum seconds between loading the form and submitting it. Bots
        // post instantly; people do not.
        'min_fill_seconds' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Post categories
    |--------------------------------------------------------------------------
    |
    | Labels live in lang/{locale}/blog.php under `categories`.
    |
    */

    'post_categories' => ['engineering', 'mobile', 'ai', 'product', 'company'],

    /*
    |--------------------------------------------------------------------------
    | About
    |--------------------------------------------------------------------------
    |
    | `values` keys resolve to lang/{locale}/about.php.
    |
    | `milestones` is empty on purpose. Real company milestones have not
    | been supplied, and inventing a founding story would be fabrication,
    | so the timeline section hides itself until entries are added:
    |
    |     ['year' => 2021, 'key' => 'founded'],
    |
    | with the copy under `about.milestones.founded` in the lang files.
    |
    */

    'about' => [
        'values' => [
            ['key' => 'ship', 'icon' => 'rocket'],
            ['key' => 'ownership', 'icon' => 'shield'],
            ['key' => 'clarity', 'icon' => 'magnifier'],
            ['key' => 'handover', 'icon' => 'code'],
        ],

        'milestones' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Careers
    |--------------------------------------------------------------------------
    |
    | Off by default. The client has not said whether they are hiring, and
    | a careers page with no roles and no intent behind it is worse than
    | none. Set CAREERS_ENABLED=true to publish the page and its open
    | application form, which captures into `leads` with source "career".
    |
    */

    'careers' => [
        'enabled' => env('CAREERS_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Scoping wizard
    |--------------------------------------------------------------------------
    |
    | Drives the multi-step form at /start-a-project. Keys are validated
    | against these lists server-side and stored on the lead's payload;
    | labels live in lang/{locale}/estimator.php.
    |
    | Note this collects scope, not a price. Publishing an automatic
    | estimate would mean inventing figures nobody has supplied. Once real
    | pricing exists, the same answers can drive one.
    |
    */

    'estimator' => [
        'platforms' => ['ios', 'android', 'web', 'admin'],

        'features' => [
            'accounts', 'payments', 'subscriptions', 'chat', 'calls',
            'notifications', 'maps', 'offline', 'ai', 'multilingual',
            'integrations', 'reporting',
        ],

        'stages' => ['idea', 'designs-ready', 'existing-product', 'rescue'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Delivery process
    |--------------------------------------------------------------------------
    |
    | Keys resolve to `lang/{locale}/home.php` under `process.steps`, so the
    | wording lives with the rest of the copy. Reorder or trim this list to
    | change the timeline.
    |
    */

    'process' => [
        ['key' => 'discovery', 'icon' => 'magnifier'],
        ['key' => 'design', 'icon' => 'pencil'],
        ['key' => 'build', 'icon' => 'code'],
        ['key' => 'quality', 'icon' => 'beaker'],
        ['key' => 'release', 'icon' => 'rocket'],
        ['key' => 'support', 'icon' => 'lifebuoy'],
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
