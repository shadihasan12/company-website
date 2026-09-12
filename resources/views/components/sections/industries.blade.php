@props(['industries'])

@php
    use App\Support\Nav;
@endphp

@if ($industries->isNotEmpty())
    <x-ui.section id="industries" tone="raised" size="compact">
        <x-ui.container>
            <x-ui.section-heading
                :eyebrow="__('home.industries.eyebrow')"
                :title="__('home.industries.title')"
                align="center"
            >
                {{ __('home.industries.subhead') }}
            </x-ui.section-heading>

            <ul class="mt-12 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4" data-reveal-group>
                @foreach ($industries as $industry)
                    <li>
                        <a
                            href="{{ Nav::link('work.index', '#work') }}"
                            class="group flex h-full flex-col items-center gap-3 rounded-2xl bg-surface p-6 text-center ring-1 ring-hairline transition-all duration-300 ease-out-expo hover:-translate-y-1 hover:ring-brand-400/40"
                        >
                            <span class="grid size-11 place-items-center rounded-xl bg-brand-500/12 text-brand-300 transition-colors duration-300 group-hover:bg-accent-500/15 group-hover:text-accent-400">
                                <x-ui.icon :name="$industry->icon ?: 'building'" size="size-5" />
                            </span>

                            <span class="text-sm font-semibold text-balance text-content">{{ $industry->name }}</span>

                            <span class="mt-auto text-xs text-content-subtle">
                                {{ trans_choice('home.industries.count', $industry->projects_count, ['count' => $industry->projects_count]) }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </x-ui.container>
    </x-ui.section>
@endif
