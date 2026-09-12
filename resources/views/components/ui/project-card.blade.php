@props(['project'])

@php
    use App\Support\Nav;

    // Up to two metrics — more turns the card into a table and stops
    // being scannable.
    $metrics = collect($project->metrics ?? [])->take(2);

    $storeLinks = array_filter([
        'website' => $project->website_url,
        'appstore' => $project->app_store_url,
        'googleplay' => $project->google_play_url,
    ]);
@endphp

<x-ui.card :href="route('work.show', $project->slug)" class="p-5">
    <x-ui.project-thumb :project="$project" />

    <div class="mt-5 flex flex-wrap items-center gap-2">
        @if ($project->industry)
            <x-ui.badge tone="iris">{{ $project->industry->name }}</x-ui.badge>
        @endif

        @if ($storeLinks)
            <x-ui.badge>{{ trans_choice('home.work.live_on', count($storeLinks)) }}</x-ui.badge>
        @endif
    </div>

    <h3 class="mt-3 font-display text-xl leading-snug font-bold text-balance">
        {{ $project->name }}
    </h3>

    @if ($project->client)
        <p class="mt-1 text-sm text-content-subtle">{{ $project->client->display_name }}</p>
    @endif

    <p class="mt-3 grow text-sm leading-relaxed text-content-muted">
        {{ Str::limit((string) $project->summary, 130) }}
    </p>

    @if ($metrics->isNotEmpty())
        <dl class="mt-5 flex flex-wrap gap-x-8 gap-y-3 border-t border-hairline pt-4">
            @foreach ($metrics as $metric)
                <div>
                    <dd class="font-display text-2xl font-bold text-brand-gradient">
                        {{ $metric['prefix'] ?? '' }}{{ number_format((float) $metric['value'], (int) ($metric['decimals'] ?? 0)) }}{{ $metric['suffix'] ?? '' }}
                    </dd>
                    <dt class="mt-0.5 text-xs text-content-subtle">{{ $metric['label'] }}</dt>
                </div>
            @endforeach
        </dl>
    @endif

    <ul class="mt-5 flex flex-wrap gap-1.5">
        @foreach ($project->technologies->take(4) as $technology)
            <li class="rounded-md bg-surface-sunken px-2 py-1 font-mono text-[11px] text-content-subtle">
                {{ $technology->name }}
            </li>
        @endforeach
    </ul>
</x-ui.card>
