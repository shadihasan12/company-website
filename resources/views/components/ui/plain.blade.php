{{-- Renders its slot with no wrapper element beyond a span, so a component
     can be conditionally wrapped in a link without branching the markup. --}}
@props(['href' => null])

<span {{ $attributes->class(['inline-flex']) }}>{{ $slot }}</span>
