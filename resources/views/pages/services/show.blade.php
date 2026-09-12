@php
    use App\Support\Nav;
@endphp

<x-layouts.app :title="(string) $service->title" :description="(string) $service->excerpt">
    {{-- Hero ---------------------------------------------------------- --}}
    <x-ui.section size="compact">
        <div class="glow -top-32 start-1/3 size-[30rem] bg-brand-500/25" aria-hidden="true"></div>

        <x-ui.container class="relative">
            <nav aria-label="Breadcrumb" class="mb-6">
                <ol class="flex items-center gap-2 text-sm text-content-subtle">
                    <li><a href="{{ route('home') }}" class="transition-colors hover:text-content">{{ config('site.name') }}</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('services.index') }}" class="transition-colors hover:text-content">{{ __('nav.services') }}</a></li>
                </ol>
            </nav>

            <div class="grid gap-10 lg:grid-cols-12 lg:gap-12">
                <div class="lg:col-span-7">
                    <span class="grid size-14 place-items-center rounded-2xl bg-brand-500/12 text-brand-300">
                        <x-ui.icon :name="$service->icon" size="size-7" />
                    </span>

                    <h1 class="mt-6 font-display text-4xl leading-tight font-bold text-balance sm:text-5xl">
                        {{ $service->title }}
                    </h1>

                    @if (! $service->tagline->isEmpty())
                        <p class="mt-3 text-lg font-medium text-accent-400">{{ $service->tagline }}</p>
                    @endif

                    <p class="mt-5 text-lg leading-relaxed text-content-muted text-pretty">
                        {{ $service->excerpt }}
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <x-ui.button :href="Nav::contact()" size="lg" icon="arrow-right">
                            {{ __('services_page.show.cta') }}
                        </x-ui.button>
                    </div>
                </div>

                {{-- Engagement facts. Pricing only appears once a figure
                     exists; until then the row reads "priced per project"
                     rather than inventing a number. --}}
                <dl class="glass flex flex-col gap-5 self-start rounded-2xl p-6 lg:col-span-5">
                    @if (! $service->timeline->isEmpty())
                        <div>
                            <dt class="text-xs font-semibold tracking-wider text-content-subtle uppercase">
                                {{ __('services_page.show.timeline') }}
                            </dt>
                            <dd class="mt-1 font-display text-lg font-bold">{{ $service->timeline }}</dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-xs font-semibold tracking-wider text-content-subtle uppercase">
                            {{ __('services_page.show.pricing') }}
                        </dt>
                        <dd class="mt-1 font-display text-lg font-bold">
                            {{ $service->starting_price->isEmpty()
                                ? __('services_page.show.pricing_on_request')
                                : $service->starting_price }}
                        </dd>
                    </div>

                    @if ($projects->isNotEmpty())
                        <div>
                            <dt class="text-xs font-semibold tracking-wider text-content-subtle uppercase">
                                {{ __('services_page.show.work') }}
                            </dt>
                            <dd class="mt-1 font-display text-lg font-bold">
                                {{ trans_choice('home.industries.count', $projects->count(), ['count' => $projects->count()]) }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- Overview and inclusions ---------------------------------------- --}}
    @if (! $service->body->isEmpty() || $service->inclusions)
        <x-ui.section tone="raised" size="compact">
            <x-ui.container>
                <div class="grid gap-12 lg:grid-cols-12">
                    @if (! $service->body->isEmpty())
                        <div class="lg:col-span-5" data-reveal="up">
                            <p class="text-lg leading-relaxed text-content-muted text-pretty">
                                {{ $service->body }}
                            </p>
                        </div>
                    @endif

                    @if ($service->inclusions)
                        <div class="lg:col-span-7" data-reveal="up">
                            <h2 class="font-display text-2xl font-bold sm:text-3xl">
                                {{ __('services_page.show.included') }}
                            </h2>

                            <ul class="mt-6 grid gap-3 sm:grid-cols-2">
                                @foreach ($service->inclusions as $inclusion)
                                    <li class="flex items-start gap-3 rounded-xl bg-surface p-4 ring-1 ring-hairline">
                                        <x-ui.icon name="check" size="size-5" class="mt-0.5 shrink-0 text-accent-400" />
                                        <span class="text-sm leading-relaxed text-content-muted">{{ $inclusion }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </x-ui.container>
        </x-ui.section>
    @endif

    <x-sections.process />

    {{-- Stack actually used on this service's work --------------------- --}}
    @if ($technologies->isNotEmpty())
        <x-ui.section tone="raised" size="compact">
            <x-ui.container>
                <h2 class="font-display text-2xl font-bold sm:text-3xl" data-reveal="up">
                    {{ __('services_page.show.stack') }}
                </h2>

                <ul class="mt-6 flex flex-wrap gap-2" data-reveal="up">
                    @foreach ($technologies as $technology)
                        <li class="rounded-lg bg-surface px-3 py-1.5 text-sm text-content-muted ring-1 ring-hairline">
                            {{ $technology->name }}
                        </li>
                    @endforeach
                </ul>
            </x-ui.container>
        </x-ui.section>
    @endif

    {{-- Related work. Hidden entirely when this service has no case study
         behind it — better a shorter page than an empty claim. --}}
    @if ($projects->isNotEmpty())
        <x-ui.section size="compact">
            <x-ui.container>
                <h2 class="font-display text-2xl font-bold sm:text-3xl" data-reveal="up">
                    {{ __('services_page.show.work') }}
                </h2>

                <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3" data-reveal-group>
                    @foreach ($projects as $project)
                        <x-ui.project-card :project="$project" />
                    @endforeach
                </div>
            </x-ui.container>
        </x-ui.section>
    @endif

    {{-- FAQs ----------------------------------------------------------- --}}
    @if ($service->faqs)
        <x-ui.section tone="raised" size="compact">
            <x-ui.container size="narrow">
                <x-ui.faqs :faqs="$service->faqs" :title="__('services_page.show.faqs')" data-reveal="up" />
            </x-ui.container>
        </x-ui.section>
    @endif

    {{-- Other services -------------------------------------------------- --}}
    @if ($others->isNotEmpty())
        <x-ui.section size="compact">
            <x-ui.container>
                <h2 class="font-display text-2xl font-bold sm:text-3xl" data-reveal="up">
                    {{ __('services_page.show.others') }}
                </h2>

                <ul class="mt-6 flex flex-wrap gap-2" data-reveal-group>
                    @foreach ($others as $other)
                        <li>
                            <a
                                href="{{ route('services.show', $other->slug) }}"
                                class="inline-flex items-center gap-2 rounded-full bg-surface-raised px-4 py-2 text-sm text-content-muted ring-1 ring-hairline transition-colors hover:text-content hover:ring-brand-400/40"
                            >
                                <x-ui.icon :name="$other->icon" size="size-4" class="text-brand-300" />
                                {{ $other->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </x-ui.container>
        </x-ui.section>
    @endif

    <x-sections.cta />
</x-layouts.app>
