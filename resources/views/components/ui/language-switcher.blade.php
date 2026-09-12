@php
    $alternates = \App\Support\Locale::alternates();
@endphp

@foreach ($alternates as $alternate)
    <a
        href="{{ $alternate['url'] }}"
        hreflang="{{ $alternate['code'] }}"
        lang="{{ $alternate['code'] }}"
        dir="{{ $alternate['dir'] }}"
        aria-label="{{ __('nav.switch_language') }}: {{ $alternate['native'] }}"
        {{ $attributes->class([
            'inline-flex h-10 items-center gap-1.5 rounded-full px-3 text-sm font-medium text-content-muted',
            'transition-colors hover:bg-surface-raised hover:text-content',
        ]) }}
    >
        <x-ui.icon name="globe-alt" size="size-4" />
        {{ $alternate['native'] }}
    </a>
@endforeach
