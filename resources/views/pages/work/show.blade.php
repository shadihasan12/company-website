@php
    use App\Support\Nav;
@endphp

<x-layouts.app
    :title="$project->name"
    :description="(string) $project->summary"
    :image="$project->hero_image_path ?? ($project->gallery[0] ?? null)"
    type="article"
>
    <x-slot:head>
        <script type="application/ld+json">
            {!! json_encode(array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'CreativeWork',
                'name' => $project->name,
                'description' => (string) $project->summary,
                'url' => \App\Support\Seo::canonical(),
                'creator' => ['@id' => url('/').'#organization'],
                'datePublished' => $project->completed_at?->toDateString(),
                'keywords' => $project->technologies->pluck('name')->implode(', ') ?: null,
            ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>

        <script type="application/ld+json">
            {!! json_encode(\App\Support\Seo::breadcrumbs([
                config('site.name') => route('home'),
                __('nav.work') => route('work.index'),
                $project->name => \App\Support\Seo::canonical(),
            ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    </x-slot:head>

    {{-- Hero ----------------------------------------------------------- --}}
    <x-ui.section size="compact">
        <div class="glow -top-32 start-1/3 size-[30rem] bg-brand-500/25" aria-hidden="true"></div>

        <x-ui.container class="relative">
            <nav aria-label="Breadcrumb" class="mb-6">
                <ol class="flex items-center gap-2 text-sm text-content-subtle">
                    <li><a href="{{ route('home') }}" class="transition-colors hover:text-content">{{ config('site.name') }}</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('work.index') }}" class="transition-colors hover:text-content">{{ __('nav.work') }}</a></li>
                </ol>
            </nav>

            <div class="grid gap-10 lg:grid-cols-12 lg:gap-12">
                <div class="lg:col-span-7">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($project->services as $service)
                            <x-ui.badge tone="brand">{{ $service->title }}</x-ui.badge>
                        @endforeach
                    </div>

                    <h1 class="mt-5 font-display text-4xl leading-tight font-bold text-balance sm:text-5xl">
                        {{ $project->name }}
                    </h1>

                    <p class="mt-5 text-lg leading-relaxed text-content-muted text-pretty">
                        {{ $project->summary }}
                    </p>

                    <x-ui.store-links :project="$project" size="lg" class="mt-8" />
                </div>

                <dl class="glass flex flex-col gap-5 self-start rounded-2xl p-6 lg:col-span-5">
                    @if ($project->client)
                        <div>
                            <dt class="text-xs font-semibold tracking-wider text-content-subtle uppercase">{{ __('work.show.client') }}</dt>
                            <dd class="mt-1 font-display text-lg font-bold">{{ $project->client->display_name }}</dd>
                        </div>
                    @endif

                    @if ($project->industry)
                        <div>
                            <dt class="text-xs font-semibold tracking-wider text-content-subtle uppercase">{{ __('work.show.industry') }}</dt>
                            <dd class="mt-1 font-display text-lg font-bold">{{ $project->industry->name }}</dd>
                        </div>
                    @endif

                    @if (! $project->duration->isEmpty())
                        <div>
                            <dt class="text-xs font-semibold tracking-wider text-content-subtle uppercase">{{ __('work.show.duration') }}</dt>
                            <dd class="mt-1 font-display text-lg font-bold">{{ $project->duration }}</dd>
                        </div>
                    @endif

                    @if ($project->completed_at)
                        <div>
                            <dt class="text-xs font-semibold tracking-wider text-content-subtle uppercase">{{ __('work.show.delivered') }}</dt>
                            <dd class="mt-1 font-display text-lg font-bold">{{ $project->completed_at->isoFormat('MMMM Y') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            @if ($project->hero_image_path)
                {{-- Above the fold and usually the LCP element, so it is
                     fetched eagerly and at high priority. --}}
                <img
                    src="{{ \Illuminate\Support\Facades\Storage::url($project->hero_image_path) }}"
                    alt="{{ $project->name }}"
                    fetchpriority="high"
                    decoding="async"
                    class="mt-12 w-full rounded-2xl object-cover ring-1 ring-hairline"
                >
            @endif
        </x-ui.container>
    </x-ui.section>

    {{-- Results -------------------------------------------------------- --}}
    @if ($project->metrics)
        <section class="border-y border-hairline bg-surface-sunken py-14">
            <x-ui.container>
                <h2 class="text-xs font-semibold tracking-[0.2em] text-content-subtle uppercase">{{ __('work.show.results') }}</h2>

                <dl class="mt-8 grid grid-cols-2 gap-y-8 lg:grid-cols-4" data-reveal-group>
                    @foreach ($project->metrics as $metric)
                        <div>
                            <dd
                                class="font-display text-4xl font-bold text-brand-gradient sm:text-5xl"
                                data-countup="{{ $metric['value'] }}"
                                data-decimals="{{ $metric['decimals'] ?? 0 }}"
                                @isset($metric['prefix']) data-prefix="{{ $metric['prefix'] }}" @endisset
                                @isset($metric['suffix']) data-suffix="{{ $metric['suffix'] }}" @endisset
                            >{{ $metric['prefix'] ?? '' }}{{ number_format((float) $metric['value'], (int) ($metric['decimals'] ?? 0)) }}{{ $metric['suffix'] ?? '' }}</dd>
                            <dt class="mt-1 text-sm text-content-muted">{{ $metric['label'] }}</dt>
                        </div>
                    @endforeach
                </dl>
            </x-ui.container>
        </section>
    @endif

    {{-- Narrative ------------------------------------------------------ --}}
    @if (! $project->problem->isEmpty() || ! $project->solution->isEmpty() || ! $project->outcome->isEmpty())
        <x-ui.section size="compact">
            <x-ui.container size="narrow">
                <div class="flex flex-col gap-12">
                    @foreach ([
                        'problem' => $project->problem,
                        'solution' => $project->solution,
                        'outcome' => $project->outcome,
                    ] as $key => $value)
                        @continue($value->isEmpty())

                        <div data-reveal="up">
                            <h2 class="font-display text-2xl font-bold sm:text-3xl">{{ __("work.show.{$key}") }}</h2>
                            <div class="mt-4 leading-relaxed text-content-muted text-pretty">{{ $value }}</div>
                        </div>
                    @endforeach
                </div>
            </x-ui.container>
        </x-ui.section>
    @endif

    {{-- Gallery -------------------------------------------------------- --}}
    @if ($project->gallery)
        <x-ui.section tone="raised" size="compact">
            <x-ui.container>
                <x-ui.gallery :images="$project->gallery" :title="__('work.gallery.title')" data-reveal="up" />
            </x-ui.container>
        </x-ui.section>
    @endif

    {{-- Testimonial ---------------------------------------------------- --}}
    @if ($testimonial)
        <x-ui.section size="compact">
            <x-ui.container size="narrow">
                <figure class="ring-gradient rounded-2xl bg-surface-raised p-8 sm:p-10" data-reveal="up">
                    <blockquote class="font-display text-xl leading-relaxed text-balance sm:text-2xl">
                        &ldquo;{{ $testimonial->quote }}&rdquo;
                    </blockquote>

                    <figcaption class="mt-6 text-sm text-content-subtle">
                        <span class="font-semibold text-content">{{ $testimonial->author_name }}</span>
                        @if (! $testimonial->author_title->isEmpty())
                            · {{ $testimonial->author_title }}
                        @endif
                    </figcaption>
                </figure>
            </x-ui.container>
        </x-ui.section>
    @endif

    {{-- Stack ---------------------------------------------------------- --}}
    @if ($project->technologies->isNotEmpty())
        <x-ui.section tone="raised" size="compact">
            <x-ui.container>
                <h2 class="font-display text-2xl font-bold sm:text-3xl" data-reveal="up">{{ __('work.show.stack') }}</h2>

                <ul class="mt-6 flex flex-wrap gap-2" data-reveal="up">
                    @foreach ($project->technologies as $technology)
                        <li>
                            <a
                                href="{{ route('work.index', ['technology' => $technology->slug]) }}"
                                class="inline-block rounded-lg bg-surface px-3 py-1.5 text-sm text-content-muted ring-1 ring-hairline transition-colors hover:text-content hover:ring-brand-400/40"
                            >{{ $technology->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </x-ui.container>
        </x-ui.section>
    @endif

    {{-- Next ----------------------------------------------------------- --}}
    @if ($next)
        <x-ui.section size="compact">
            <x-ui.container>
                <a
                    href="{{ route('work.show', $next->slug) }}"
                    class="group flex flex-col gap-2 rounded-2xl bg-surface-raised p-8 ring-1 ring-hairline transition-all duration-300 ease-out-expo hover:-translate-y-1 hover:ring-brand-400/40 sm:p-10"
                >
                    <span class="text-xs font-semibold tracking-wider text-content-subtle uppercase">{{ __('work.show.next') }}</span>
                    <span class="flex items-center gap-3 font-display text-2xl font-bold text-balance sm:text-3xl">
                        {{ $next->name }}
                        <x-ui.icon name="arrow-right" size="size-6" class="shrink-0 text-accent-400 transition-transform duration-300 group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1" />
                    </span>
                </a>
            </x-ui.container>
        </x-ui.section>
    @endif

    <x-sections.cta />
</x-layouts.app>
