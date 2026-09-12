<x-layouts.app :title="__('work.index.title')" :description="__('work.index.subhead')">
    <x-ui.section size="compact">
        <div class="glow -top-32 end-1/4 size-[30rem] bg-accent-500/20" aria-hidden="true"></div>

        <x-ui.container class="relative">
            <x-ui.section-heading
                :eyebrow="__('nav.work')"
                :title="__('work.index.heading')"
                as="h1"
                align="center"
            >
                {{ __('work.index.subhead') }}
            </x-ui.section-heading>

            <div class="mt-12 flex flex-col gap-3 rounded-2xl bg-surface-raised p-5 ring-1 ring-hairline">
                <x-ui.filter-bar
                    :label="__('work.filters.service')"
                    param="service"
                    :options="$services"
                    :active="$filters['service'] ?? null"
                />
                <x-ui.filter-bar
                    :label="__('work.filters.industry')"
                    param="industry"
                    :options="$industries"
                    :active="$filters['industry'] ?? null"
                />
                <x-ui.filter-bar
                    :label="__('work.filters.technology')"
                    param="technology"
                    :options="$technologies"
                    :active="$filters['technology'] ?? null"
                />
            </div>

            @if ($projects->isEmpty())
                <div class="mt-12 flex flex-col items-center gap-5 rounded-2xl bg-surface-raised p-14 text-center ring-1 ring-hairline">
                    <p class="text-lg text-content-muted">{{ __('work.index.empty') }}</p>
                    <x-ui.button :href="route('work.index')" variant="secondary">
                        {{ __('work.index.empty_action') }}
                    </x-ui.button>
                </div>
            @else
                <p class="mt-8 text-sm text-content-subtle">
                    {{ trans_choice('work.index.showing', $projects->total(), ['count' => $projects->total()]) }}
                </p>

                <div class="mt-5 grid gap-5 md:grid-cols-2 lg:grid-cols-3" data-reveal-group>
                    @foreach ($projects as $project)
                        <x-ui.project-card :project="$project" />
                    @endforeach
                </div>

                @if ($projects->hasPages())
                    <div class="mt-12">
                        {{ $projects->links() }}
                    </div>
                @endif
            @endif
        </x-ui.container>
    </x-ui.section>

    <x-sections.cta />
</x-layouts.app>
