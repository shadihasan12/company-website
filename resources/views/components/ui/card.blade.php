@props([
    'href' => null,
    'interactive' => true,
])

@php
    $tag = $href ? 'a' : 'div';

    $classes = [
        'group relative flex flex-col rounded-2xl bg-surface-raised p-6 ring-1 ring-hairline',
        // Never `transition-all`: the reveal animation drives opacity and
        // transform on this element, and a CSS transition on the same
        // properties fights it. Only the two the hover actually needs.
        'transition-[translate,box-shadow] duration-300 ease-out-expo' => $interactive,
        'hover:-translate-y-1 hover:ring-brand-400/40 hover:shadow-xl hover:shadow-brand-950/20' => $interactive,
    ];
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->class($classes) }}>
    {{ $slot }}
</{{ $tag }}>
