@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'icon' => null,
])

@php
    $variants = [
        // Magenta is the single highest-intent colour on the site. It is
        // reserved for the primary action so it never competes with itself.
        'primary' => 'bg-accent-500 text-white shadow-lg shadow-accent-500/25 hover:bg-accent-400 hover:shadow-accent-500/35 active:bg-accent-600',
        'secondary' => 'bg-surface-raised text-content ring-1 ring-hairline hover:bg-surface-sunken hover:ring-brand-400/40',
        'ghost' => 'text-content-muted hover:bg-surface-raised hover:text-content',
        'gradient' => 'bg-brand-gradient text-white shadow-lg shadow-brand-500/25 hover:brightness-110',
        'outline' => 'ring-gradient rounded-full bg-transparent text-content hover:bg-surface-raised',
    ];

    $sizes = [
        'sm' => 'h-9 px-4 text-sm gap-1.5',
        'md' => 'h-11 px-6 text-sm gap-2',
        'lg' => 'h-13 px-8 text-base gap-2.5',
    ];

    $classes = [
        'inline-flex shrink-0 items-center justify-center rounded-full font-medium whitespace-nowrap',
        'transition-all duration-200 ease-out-expo',
        'hover:-translate-y-0.5 active:translate-y-0',
        'disabled:pointer-events-none disabled:opacity-50',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
    ];

    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @else type="{{ $attributes->get('type', 'button') }}" @endif
    {{ $attributes->class($classes) }}
>
    {{ $slot }}

    @if ($icon)
        {{-- Arrows mirror in RTL so "forward" still points forward in Arabic. --}}
        <x-ui.icon :name="$icon" size="size-4" class="rtl:-scale-x-100" />
    @endif
</{{ $tag }}>
