@props(['technologies'])

@php
    // Fixed order so the columns do not reshuffle as rows are edited.
    $categories = ['language', 'framework', 'service', 'tool'];
@endphp

@if ($technologies->isNotEmpty())
    <x-ui.section id="stack" tone="raised" size="compact">
        <x-ui.container>
            <x-ui.section-heading
                :eyebrow="__('home.tech.eyebrow')"
                :title="__('home.tech.title')"
                align="center"
            >
                {{ __('home.tech.subhead') }}
            </x-ui.section-heading>

            <div class="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-4" data-reveal-group>
                @foreach ($categories as $category)
                    @continue($technologies->get($category)?->isEmpty() ?? true)

                    <div>
                        <h3 class="text-xs font-semibold tracking-[0.16em] text-content-subtle uppercase">
                            {{ __("home.tech.categories.{$category}") }}
                        </h3>

                        <ul class="mt-4 flex flex-wrap gap-2">
                            @foreach ($technologies->get($category) as $technology)
                                <li class="rounded-lg bg-surface px-3 py-1.5 text-sm text-content-muted ring-1 ring-hairline transition-colors duration-200 hover:text-content hover:ring-brand-400/40">
                                    {{ $technology->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>
@endif
