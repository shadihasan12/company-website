@props([
    'title' => null,
    'description' => null,
])

@php
    use App\Support\Locale;

    $locale = Locale::current();
    $siteName = config('site.name');
    $pageTitle = $title ? "{$title} — {$siteName}" : $siteName;

    // The Arabic face is ~90KB and is only needed on RTL pages, so it is
    // requested only there. Latin faces load everywhere — Arabic pages still
    // render Latin brand names, tech terms and numerals.
    $fontAliases = Locale::isRtl()
        ? ['inter', 'space-grotesk', 'ibm-plex-sans-arabic']
        : ['inter', 'space-grotesk'];
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

    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif

    {{-- Full SEO/OG/JSON-LD handling lands in T7.1–T7.2. --}}
    <meta name="robots" content="noindex, nofollow">

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

    {{ $head ?? '' }}
    @stack('schema')
</head>
<body class="min-h-dvh antialiased">
    <x-layout.header />

    <main id="main">
        {{ $slot }}
    </main>

    <x-layout.footer />
</body>
</html>
