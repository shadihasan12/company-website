<x-layouts.app :description="__('home.hero.subhead')">
    <x-sections.hero :shipped-count="$shippedCount" />

    {{-- Sections below are still the T0.2 data preview; T1.2–T1.10 replace
         them with their designed equivalents. --}}

    <x-ui.section tone="raised" size="compact">
        <x-ui.container>
            <x-ui.section-heading :title="__('home.services_title')">
                {{ __('home.services_note') }}
            </x-ui.section-heading>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3" data-reveal-group>
                @foreach ($services as $service)
                    <x-ui.card>
                        <span class="grid size-11 place-items-center rounded-xl bg-brand-500/12 text-brand-300 transition-colors group-hover:bg-accent-500/15 group-hover:text-accent-400">
                            <x-ui.icon :name="$service->icon" size="size-5" />
                        </span>

                        <h3 class="mt-5 font-display text-lg font-bold">{{ $service->title }}</h3>
                        <p class="mt-1 text-sm text-accent-400">{{ $service->tagline }}</p>
                        <p class="mt-3 grow text-sm leading-relaxed text-content-muted">{{ $service->excerpt }}</p>
                    </x-ui.card>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    <x-ui.section size="compact" id="work">
        <x-ui.container>
            <x-ui.section-heading :title="__('home.work_title')">
                {{ __('home.work_note', ['command' => 'php artisan content:audit']) }}
            </x-ui.section-heading>

            <div class="mt-10 grid gap-5 lg:grid-cols-3" data-reveal-group>
                @foreach ($featuredProjects as $project)
                    <x-ui.card>
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($project->industry)
                                <x-ui.badge tone="iris">{{ $project->industry->name }}</x-ui.badge>
                            @endif

                            @unless ($project->is_evidenced)
                                {{-- Visible in local development so missing proof
                                     is impossible to overlook. --}}
                                <x-ui.badge tone="accent">Needs screenshots &amp; results</x-ui.badge>
                            @endunless
                        </div>

                        <h3 class="mt-4 font-display text-lg font-bold text-balance">{{ $project->name }}</h3>

                        @if ($project->client)
                            <p class="mt-1 text-sm text-content-subtle">{{ $project->client->display_name }}</p>
                        @endif

                        <p class="mt-3 grow text-sm leading-relaxed text-content-muted">{{ $project->summary }}</p>

                        <ul class="mt-5 flex flex-wrap gap-1.5">
                            @foreach ($project->technologies->take(5) as $technology)
                                <li class="rounded-md bg-surface-sunken px-2 py-1 font-mono text-[11px] text-content-subtle">
                                    {{ $technology->name }}
                                </li>
                            @endforeach
                        </ul>

                        @if ($project->website_url)
                            <a
                                href="{{ $project->website_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-5 inline-flex items-center gap-1.5 text-sm font-medium text-accent-400 hover:text-accent-300"
                            >
                                {{ parse_url($project->website_url, PHP_URL_HOST) }}
                                <x-ui.icon name="arrow-up-right" size="size-4" />
                            </a>
                        @endif
                    </x-ui.card>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>
</x-layouts.app>
