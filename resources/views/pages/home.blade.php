<x-layouts.app :description="__('home.subhead')">
    <x-ui.section size="tall" class="text-center">
        {{-- Ambient brand glows. Decorative, pointer-inert, and positioned
             with logical properties so they mirror correctly in Arabic. --}}
        <div class="glow -top-24 start-1/4 size-[32rem] bg-iris-500/30" aria-hidden="true"></div>
        <div class="glow top-40 end-1/4 size-[28rem] bg-accent-500/20" aria-hidden="true"></div>

        <x-ui.container class="relative">
            <div class="mx-auto flex max-w-3xl flex-col items-center gap-7">
                <x-ui.badge tone="accent" data-reveal="up">
                    {{ __('home.eyebrow') }}
                </x-ui.badge>

                <h1
                    class="font-display text-4xl leading-[1.1] font-bold text-balance sm:text-6xl lg:text-7xl"
                    data-reveal="up"
                >
                    {{ __('home.headline') }}
                </h1>

                <p class="max-w-2xl text-lg leading-relaxed text-content-muted text-pretty sm:text-xl" data-reveal="up">
                    {{ __('home.subhead') }}
                </p>

                <div class="flex flex-wrap items-center justify-center gap-3" data-reveal="up">
                    <x-ui.button href="{{ route('styleguide') }}" size="lg" icon="arrow-right">
                        {{ __('home.view_styleguide') }}
                    </x-ui.button>

                    <x-ui.button href="mailto:{{ config('site.contact.email') }}" variant="secondary" size="lg">
                        {{ __('common.get_in_touch') }}
                    </x-ui.button>
                </div>

                <p class="text-xs text-content-subtle" data-reveal="up">
                    {{ __('home.shell_note') }}
                </p>
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- ------------------------------------------------------------------
         T0.2 preview: everything below is read from the database, proving
         the model, the translatable casts and the relationships end to end.
         Epic 1 replaces this with the designed homepage.
         ------------------------------------------------------------------ --}}

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

    <x-ui.section size="compact">
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
