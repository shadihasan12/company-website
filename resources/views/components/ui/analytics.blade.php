@php
    $ga4 = config('services.ga4.id');
    $plausible = config('services.plausible.domain');

    // Set by the lead-capture flows and read once, so a conversion is
    // reported on the page the visitor actually lands on.
    $conversion = session('conversion');
@endphp

@if ($verification = config('services.search_console.verification'))
    <meta name="google-site-verification" content="{{ $verification }}">
@endif

{{-- Analytics is skipped entirely outside production. Local and staging
     traffic in a client's reports is worse than no data. --}}
@if (app()->isProduction())
    @if ($plausible)
        <script defer data-domain="{{ $plausible }}" src="{{ rtrim(config('services.plausible.host'), '/') }}/js/script.js"></script>
        <script>
            window.plausible = window.plausible || function () { (window.plausible.q = window.plausible.q || []).push(arguments) };
        </script>
    @endif

    @if ($ga4)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4 }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments)}
            gtag('js', new Date());
            gtag('config', @json($ga4));
        </script>
    @endif

    @if ($conversion && ($ga4 || $plausible))
        {{-- A conversion is only ever reported once, from the server's
             flash, so a refresh of the thank-you page cannot inflate it. --}}
        <script>
            (function () {
                var event = @json($conversion['event']);
                var props = @json($conversion['properties'] ?? []);

                if (window.gtag) { gtag('event', event, props) }
                if (window.plausible) { plausible(event, { props: props }) }
            })();
        </script>
    @endif
@endif
