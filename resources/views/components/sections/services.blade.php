@props(['services'])

@php
    use App\Support\Nav;
@endphp

<x-ui.section id="services" size="compact">
    <x-ui.container>
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <x-ui.section-heading
                :eyebrow="__('home.services.eyebrow')"
                :title="__('home.services.title')"
            >
                {{ __('home.services.subhead') }}
            </x-ui.section-heading>

            <x-ui.button
                :href="Nav::link('services.index', '#services')"
                variant="secondary"
                icon="arrow-right"
                class="shrink-0"
                data-reveal="up"
            >
                {{ __('nav.all_services') }}
            </x-ui.button>
        </div>

        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3" data-reveal-group>
            @foreach ($services as $service)
                <x-ui.card :href="Nav::link('services.show', '#services')">
                    <span class="grid size-12 place-items-center rounded-xl bg-brand-500/12 text-brand-300 transition-colors duration-300 group-hover:bg-accent-500/15 group-hover:text-accent-400">
                        <x-ui.icon :name="$service->icon" size="size-6" />
                    </span>

                    <h3 class="mt-5 font-display text-xl font-bold">{{ $service->title }}</h3>

                    @if (! $service->tagline->isEmpty())
                        <p class="mt-1 text-sm font-medium text-accent-400">{{ $service->tagline }}</p>
                    @endif

                    <p class="mt-3 grow text-sm leading-relaxed text-content-muted">{{ $service->excerpt }}</p>

                    <span class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-content-subtle transition-colors group-hover:text-accent-400">
                        {{ __('common.learn_more') }}
                        <x-ui.icon
                            name="arrow-right"
                            size="size-4"
                            class="transition-transform duration-300 group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1"
                        />
                    </span>
                </x-ui.card>
            @endforeach
        </div>
    </x-ui.container>
</x-ui.section>
