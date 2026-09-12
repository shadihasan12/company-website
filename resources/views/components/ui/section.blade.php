@props([
    'tone' => 'surface',
    'size' => 'default',
])

@php
    $background = match ($tone) {
        'raised' => 'bg-surface-raised',
        'sunken' => 'bg-surface-sunken',
        'gradient' => 'bg-brand-gradient text-white',
        default => 'bg-surface',
    };

    $padding = match ($size) {
        'compact' => 'py-14 sm:py-16',
        'tall' => 'py-24 sm:py-32 lg:py-40',
        default => 'py-20 sm:py-24 lg:py-28',
    };
@endphp

<section {{ $attributes->class(['relative overflow-hidden', $background, $padding]) }}>
    {{ $slot }}
</section>
