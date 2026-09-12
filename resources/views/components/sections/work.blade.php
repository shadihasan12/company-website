@props(['projects'])

@php
    use App\Support\Nav;
@endphp

@if ($projects->isNotEmpty())
    <x-ui.section id="work" tone="raised" size="compact">
        <x-ui.container>
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <x-ui.section-heading
                    :eyebrow="__('home.work.eyebrow')"
                    :title="__('home.work.title')"
                >
                    {{ __('home.work.subhead') }}
                </x-ui.section-heading>

                <x-ui.button
                    :href="Nav::link('work.index', '#work')"
                    variant="secondary"
                    icon="arrow-right"
                    class="shrink-0"
                    data-reveal="up"
                >
                    {{ __('home.work.cta') }}
                </x-ui.button>
            </div>

            <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3" data-reveal-group>
                @foreach ($projects as $project)
                    <x-ui.project-card :project="$project" />
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>
@endif
