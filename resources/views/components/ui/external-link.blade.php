@props(['href'])

<a href="{{ $href }}" target="_blank" rel="noopener noreferrer" {{ $attributes->class(['inline-flex']) }}>
    {{ $slot }}
</a>
