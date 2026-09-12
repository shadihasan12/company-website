@props(['size' => 'default'])

@php
    $max = match ($size) {
        'narrow' => 'max-w-3xl',
        'wide' => 'max-w-[90rem]',
        default => 'max-w-7xl',
    };
@endphp

<div {{ $attributes->class(['mx-auto w-full px-5 sm:px-8 lg:px-12', $max]) }}>
    {{ $slot }}
</div>
