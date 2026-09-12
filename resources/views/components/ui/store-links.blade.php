@props(['project', 'size' => 'md'])

@php
    $links = array_filter([
        'website' => $project->website_url,
        'app_store' => $project->app_store_url,
        'google_play' => $project->google_play_url,
    ]);
@endphp

@if ($links)
    <div {{ $attributes->class(['flex flex-wrap gap-3']) }}>
        @foreach ($links as $kind => $url)
            <x-ui.button
                :href="$url"
                :size="$size"
                :variant="$loop->first ? 'primary' : 'secondary'"
                target="_blank"
                rel="noopener noreferrer"
                icon="arrow-up-right"
            >
                {{ __("work.links.{$kind}") }}
            </x-ui.button>
        @endforeach
    </div>
@endif
