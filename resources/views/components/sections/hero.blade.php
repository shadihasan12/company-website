@props([
    'shippedCount' => 0,
])

@php
    use App\Support\Nav;
@endphp

<section class="relative isolate overflow-hidden" data-hero>
    {{-- ----------------------------------------------------------------
         Decorative background. Entirely inert to pointers and hidden from
         assistive technology. Nothing here may become the LCP element, so
         it is all colour and transform — no images, no text.
         ---------------------------------------------------------------- --}}
    <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
        <div class="glow -top-32 start-[15%] size-[34rem] bg-iris-500/30" data-hero-glow></div>
        <div class="glow top-1/3 end-[10%] size-[30rem] bg-accent-500/25" data-hero-glow></div>
        <div class="glow -bottom-40 start-1/2 size-[28rem] bg-brand-500/25" data-hero-glow></div>

        {{-- Orbit rings echoing the logo mark. Rotation only, so the
             browser can keep them on the compositor. --}}
        <svg
            class="absolute start-1/2 top-1/2 h-[52rem] w-[52rem] -translate-x-1/2 -translate-y-1/2 opacity-[0.18] rtl:translate-x-1/2"
            viewBox="0 0 400 400"
            fill="none"
        >
            <defs>
                <linearGradient id="hero-orbit" x1="0" y1="0" x2="400" y2="400" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="var(--color-brand-iris)" />
                    <stop offset="50%" stop-color="var(--color-brand-violet)" />
                    <stop offset="100%" stop-color="var(--color-brand-magenta)" />
                </linearGradient>
            </defs>
            <ellipse cx="200" cy="200" rx="190" ry="86" stroke="url(#hero-orbit)" stroke-width="1" data-hero-orbit="28" />
            <ellipse cx="200" cy="200" rx="160" ry="150" stroke="url(#hero-orbit)" stroke-width="1" data-hero-orbit="-44" />
            <ellipse cx="200" cy="200" rx="120" ry="188" stroke="url(#hero-orbit)" stroke-width="1" data-hero-orbit="66" />
        </svg>

        {{-- Fade the background into the section below. --}}
        <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-b from-transparent to-surface"></div>
    </div>

    <x-ui.container class="relative">
        <div class="flex min-h-[calc(100dvh-4.5rem)] flex-col items-center justify-center gap-7 py-24 text-center sm:py-32">
            <x-ui.badge tone="accent" class="glass" data-hero-item>
                {{ __('home.hero.eyebrow') }}
            </x-ui.badge>

            {{-- The h1 is the LCP element. It is never faded in: an element
                 at opacity 0 does not count as painted, so a fade would push
                 LCP out by the full duration of the animation. It moves on
                 transform only, which costs nothing. --}}
            <h1
                class="font-display text-4xl leading-[1.05] font-bold tracking-tight text-balance sm:text-6xl lg:text-7xl"
                data-hero-headline
            >
                {{ __('home.hero.headline_lead') }}
                <span class="text-brand-gradient">{{ __('home.hero.headline_accent') }}</span>
            </h1>

            <p class="max-w-2xl text-lg leading-relaxed text-content-muted text-pretty sm:text-xl" data-hero-item>
                {{ __('home.hero.subhead') }}
            </p>

            <div class="flex flex-wrap items-center justify-center gap-3" data-hero-item>
                <x-ui.button :href="Nav::start()" size="lg" icon="arrow-right">
                    {{ __('home.hero.primary_cta') }}
                </x-ui.button>

                <x-ui.button href="#work" variant="secondary" size="lg">
                    {{ __('home.hero.secondary_cta') }}
                </x-ui.button>
            </div>

            @if ($shippedCount > 0)
                {{-- Counted from published case studies rather than hardcoded,
                     so the claim cannot drift away from the portfolio. --}}
                <p class="text-sm text-content-subtle" data-hero-item>
                    {{ __('home.hero.proof', ['count' => $shippedCount]) }}
                </p>
            @endif
        </div>
    </x-ui.container>

    <a
        href="#work"
        class="absolute bottom-6 start-1/2 hidden -translate-x-1/2 flex-col items-center gap-1.5 text-xs text-content-subtle transition-colors hover:text-content sm:flex rtl:translate-x-1/2"
        data-hero-item
    >
        {{ __('home.hero.scroll') }}
        <x-ui.icon name="chevron-down" size="size-4" class="animate-bounce" />
    </a>
</section>
