@php
    use App\Support\Nav;
@endphp

<x-layouts.app :title="__('services_page.index.title')" :description="__('services_page.index.subhead')">
    <x-ui.section size="compact">
        <div class="glow -top-32 start-1/4 size-[30rem] bg-iris-500/25" aria-hidden="true"></div>

        <x-ui.container class="relative">
            <x-ui.section-heading
                :eyebrow="__('nav.services')"
                :title="__('services_page.index.heading')"
                as="h1"
                align="center"
            >
                {{ __('services_page.index.subhead') }}
            </x-ui.section-heading>

            <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3" data-reveal-group>
                @foreach ($services as $service)
                    <x-ui.card :href="Nav::link('services.show', '#', $service->slug)">
                        <span class="grid size-12 place-items-center rounded-xl bg-brand-500/12 text-brand-300 transition-colors duration-300 group-hover:bg-accent-500/15 group-hover:text-accent-400">
                            <x-ui.icon :name="$service->icon" size="size-6" />
                        </span>

                        <h2 class="mt-5 font-display text-xl font-bold">{{ $service->title }}</h2>

                        @if (! $service->tagline->isEmpty())
                            <p class="mt-1 text-sm font-medium text-accent-400">{{ $service->tagline }}</p>
                        @endif

                        <p class="mt-3 grow text-sm leading-relaxed text-content-muted">{{ $service->excerpt }}</p>

                        <div class="mt-6 flex items-center justify-between gap-3">
                            <span class="inline-flex items-center gap-1.5 text-sm font-medium text-content-subtle transition-colors group-hover:text-accent-400">
                                {{ __('common.learn_more') }}
                                <x-ui.icon
                                    name="arrow-right"
                                    size="size-4"
                                    class="transition-transform duration-300 group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1"
                                />
                            </span>

                            @if ($service->projects_count > 0)
                                <span class="text-xs text-content-subtle">
                                    {{ trans_choice('home.industries.count', $service->projects_count, ['count' => $service->projects_count]) }}
                                </span>
                            @endif
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    <x-sections.process />

    <x-sections.cta />
</x-layouts.app>
