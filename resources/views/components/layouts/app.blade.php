@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'noindex' => false,
])

@php
    use App\Support\Fonts;
    use App\Support\Locale;
    use App\Support\Seo;

    $locale = Locale::current();
    $siteName = config('site.name');
    $pageTitle = $title ? "{$title} — {$siteName}" : $siteName;
    $metaDescription = $description ?: __('home.hero.subhead');
    $ogImage = Seo::image($image);
    $indexable = Seo::isIndexable() && ! $noindex;

    // The Arabic face is ~90KB and is only needed on RTL pages, so it is
    // requested only there. Latin faces load everywhere — Arabic pages still
    // render Latin brand names, tech terms and numerals.
    //
    // Filtered against what was actually built: the Arabic family is
    // commented out of vite.config.js while the locale is off, and asking
    // @fonts for a family it does not know throws.
    $fontAliases = Fonts::available(Locale::isRtl()
        ? ['inter', 'space-grotesk', 'ibm-plex-sans-arabic']
        : ['inter', 'space-grotesk']);
@endphp

<!DOCTYPE html>
{{-- `dark` is the default so the first paint is dark; the inline script
     below corrects it before paint when the visitor prefers light. --}}
<html
    lang="{{ $locale }}"
    dir="{{ Locale::direction() }}"
    class="dark scroll-pt-24"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">

    {{-- Canonical drops the query string, so filtered and paginated views
         point back at the clean listing instead of competing with it. --}}
    <link rel="canonical" href="{{ Seo::canonical() }}">

    @foreach (Seo::alternates() as $alternate)
        <link rel="alternate" hreflang="{{ $alternate['hreflang'] }}" href="{{ $alternate['href'] }}">
    @endforeach

    {{-- Defaults to the production environment, so a staging copy cannot
         be indexed by accident. --}}
    <meta name="robots" content="{{ $indexable ? 'index, follow, max-image-preview:large' : 'noindex, nofollow' }}">

    <meta property="og:type" content="{{ $type }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ Seo::canonical() }}">
    <meta property="og:locale" content="{{ $locale }}">

    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="{{ $ogImage }}">
    @else
        <meta name="twitter:card" content="summary">
    @endif

    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">

    {{-- Organization is emitted on every page: it is how search engines
         and AI assistants establish what this company is. --}}
    <script type="application/ld+json">
        {!! json_encode(['@context' => 'https://schema.org', ...Seo::organization()], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    {{-- Applied before first paint to avoid a light/dark flash. Inline and
         synchronous on purpose: a deferred script would flash. --}}
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('theme');
                var theme = (stored === 'light' || stored === 'dark')
                    ? stored
                    : (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
                document.documentElement.classList.toggle('dark', theme === 'dark');
            } catch (e) {
                // Storage blocked — keep the server-rendered dark default.
            }
        })();
    </script>

    {{-- Keeps Alpine-controlled elements hidden until Alpine boots. --}}
    <style>[x-cloak]{display:none!important}</style>

    @fonts($fontAliases)
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <x-ui.analytics />

    {{ $head ?? '' }}
    @stack('schema')
</head>
<body class="min-h-dvh antialiased">
    <x-layout.header />

    <main id="main">
        {{ $slot }}
    </main>

    <x-layout.footer />

    <x-ui.whatsapp-float />

    @stack('scripts')
</body>
</html>
