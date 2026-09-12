@php
    $whatsapp = config('site.contact.whatsapp');
@endphp

@if ($whatsapp)
    {{-- Appears only after the visitor has scrolled past the hero, so it
         never competes with the primary call to action above the fold. --}}
    <a
        href="https://wa.me/{{ $whatsapp }}"
        target="_blank"
        rel="noopener noreferrer"
        x-data="{ shown: false }"
        x-on:scroll.window.throttle.200ms="shown = window.scrollY > window.innerHeight * 0.6"
        x-show="shown"
        x-cloak
        x-transition.opacity.duration.200ms
        aria-label="{{ __('common.cta_whatsapp') }}"
        class="fixed bottom-5 end-5 z-40 grid size-13 place-items-center rounded-full bg-[#25D366] text-white shadow-lg shadow-black/25 transition-transform duration-200 hover:scale-105"
    >
        <x-ui.icon name="whatsapp" size="size-6" />
    </a>
@endif
