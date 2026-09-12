@props([
    'eyebrow' => null,
    'title',
    'align' => 'start',
    'as' => 'h2',
])

@php
    $alignment = $align === 'center'
        ? 'items-center text-center mx-auto max-w-3xl'
        : 'items-start text-start max-w-2xl';
@endphp

<div {{ $attributes->class(['flex flex-col gap-4', $alignment]) }} data-reveal="up">
    @if ($eyebrow)
        <x-ui.badge tone="accent">{{ $eyebrow }}</x-ui.badge>
    @endif

    <{{ $as }} class="font-display text-3xl leading-tight font-bold text-balance sm:text-4xl lg:text-5xl">
        {{ $title }}
    </{{ $as }}>

    @if (! $slot->isEmpty())
        <p class="text-lg leading-relaxed text-content-muted text-pretty">
            {{ $slot }}
        </p>
    @endif
</div>
