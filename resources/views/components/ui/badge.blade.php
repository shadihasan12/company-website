@props(['tone' => 'neutral'])

@php
    $tones = [
        'neutral' => 'bg-surface-raised text-content-muted ring-1 ring-hairline',
        'accent' => 'bg-accent-500/12 text-accent-400 ring-1 ring-accent-500/25',
        'brand' => 'bg-brand-500/12 text-brand-300 ring-1 ring-brand-500/25',
        'iris' => 'bg-iris-500/12 text-iris-300 ring-1 ring-iris-500/25',
    ];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium tracking-wide',
    $tones[$tone] ?? $tones['neutral'],
]) }}>
    {{ $slot }}
</span>
