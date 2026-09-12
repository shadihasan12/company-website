@props([
    'label',
    'param',
    'options',
    'active' => null,
])

@php
    // Preserve the other active filters and reset pagination when a facet
    // changes, so combinations work and page 3 of a different filter is
    // never shown.
    $urlFor = function (?string $value) use ($param) {
        $query = array_filter(array_merge(request()->except(['page', $param]), $value ? [$param => $value] : []));

        return request()->url().($query ? '?'.http_build_query($query) : '');
    };
@endphp

<div {{ $attributes->class(['flex flex-wrap items-center gap-2']) }}>
    <span class="me-1 text-xs font-semibold tracking-wider text-content-subtle uppercase">{{ $label }}</span>

    <a
        href="{{ $urlFor(null) }}"
        @class([
            'rounded-full px-3 py-1.5 text-sm transition-colors',
            'bg-accent-500 text-white' => ! $active,
            'bg-surface-raised text-content-muted ring-1 ring-hairline hover:text-content' => $active,
        ])
        @if (! $active) aria-current="true" @endif
    >
        {{ __('work.filters.all') }}
    </a>

    @foreach ($options as $option)
        <a
            href="{{ $urlFor($option->slug) }}"
            @class([
                'rounded-full px-3 py-1.5 text-sm transition-colors',
                'bg-accent-500 text-white' => $active === $option->slug,
                'bg-surface-raised text-content-muted ring-1 ring-hairline hover:text-content' => $active !== $option->slug,
            ])
            @if ($active === $option->slug) aria-current="true" @endif
        >
            {{ $option->title ?? $option->name }}
        </a>
    @endforeach
</div>
