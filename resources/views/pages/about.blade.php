@php
    use App\Support\Nav;

    $values = config('site.about.values');
    $milestones = config('site.about.milestones');
@endphp

<x-layouts.app :title="__('about.title')" :description="__('about.lead')">
    <x-ui.section size="compact">
        <div class="glow -top-32 start-1/4 size-[32rem] bg-iris-500/25" aria-hidden="true"></div>
        <div class="glow top-20 end-1/4 size-[26rem] bg-accent-500/20" aria-hidden="true"></div>

        <x-ui.container class="relative">
            <div class="max-w-3xl">
                <x-ui.badge tone="accent">{{ __('about.title') }}</x-ui.badge>

                <h1 class="mt-5 font-display text-4xl leading-tight font-bold text-balance sm:text-5xl lg:text-6xl">
                    {{ __('about.heading') }}
                </h1>

                <p class="mt-6 text-lg leading-relaxed text-content-muted text-pretty sm:text-xl">
                    {{ __('about.lead') }}
                </p>
            </div>

            <div class="mt-14 grid gap-6 lg:grid-cols-3" data-reveal-group>
                @foreach (__('about.story') as $paragraph)
                    <p class="leading-relaxed text-content-muted text-pretty">{{ $paragraph }}</p>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    <x-sections.stats :stats="$stats" />

    {{-- Values --}}
    <x-ui.section size="compact">
        <x-ui.container>
            <x-ui.section-heading :title="__('about.values_title')" align="center" />

            <div class="mt-12 grid gap-5 sm:grid-cols-2" data-reveal-group>
                @foreach ($values as $value)
                    <div class="flex gap-5 rounded-2xl bg-surface-raised p-6 ring-1 ring-hairline">
                        <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-brand-500/12 text-brand-300">
                            <x-ui.icon :name="$value['icon']" size="size-5" />
                        </span>

                        <div>
                            <h3 class="font-display text-lg font-bold">{{ __("about.values.{$value['key']}.title") }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-content-muted">
                                {{ __("about.values.{$value['key']}.body") }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- Milestones. Hidden until real entries exist — see config/site.php. --}}
    @if ($milestones)
        <x-ui.section tone="raised" size="compact">
            <x-ui.container size="narrow">
                <x-ui.section-heading :title="__('about.milestones_title')" align="center" />

                <ol class="relative mt-12" data-reveal-group>
                    <div class="pointer-events-none absolute inset-y-0 start-[4.5rem] w-px bg-gradient-to-b from-iris-500/50 to-accent-500/50" aria-hidden="true"></div>

                    @foreach ($milestones as $milestone)
                        <li class="relative flex gap-6 pb-10 last:pb-0">
                            <span class="w-16 shrink-0 text-end font-display text-lg font-bold text-brand-gradient tabular-nums">
                                {{ $milestone['year'] }}
                            </span>
                            <span class="relative z-10 mt-2 size-3 shrink-0 rounded-full bg-accent-500 ring-4 ring-surface-raised"></span>
                            <p class="text-content-muted">{{ __("about.milestones.{$milestone['key']}") }}</p>
                        </li>
                    @endforeach
                </ol>
            </x-ui.container>
        </x-ui.section>
    @endif

    <x-sections.process />

    <x-sections.client-strip :clients="$clients" />

    <x-sections.cta />
</x-layouts.app>
