@php
    $steps = config('site.process');
@endphp

@if ($steps)
    <x-ui.section id="process" size="compact">
        <x-ui.container>
            <x-ui.section-heading
                :eyebrow="__('home.process.eyebrow')"
                :title="__('home.process.title')"
                align="center"
            >
                {{ __('home.process.subhead') }}
            </x-ui.section-heading>

            <ol class="relative mx-auto mt-14 max-w-3xl" data-reveal-group>
                {{-- Rail behind the nodes. Positioned with logical
                     properties so it mirrors in Arabic, and hidden from
                     assistive technology since the ordered list already
                     conveys the sequence. --}}
                <div
                    class="pointer-events-none absolute inset-y-0 start-5 w-px bg-gradient-to-b from-iris-500/50 via-brand-500/50 to-accent-500/50 sm:start-6"
                    aria-hidden="true"
                ></div>

                @foreach ($steps as $index => $step)
                    <li class="relative flex gap-5 pb-10 last:pb-0 sm:gap-7">
                        <span class="relative z-10 grid size-10 shrink-0 place-items-center rounded-full bg-surface-raised text-brand-300 ring-1 ring-hairline sm:size-12">
                            <x-ui.icon :name="$step['icon']" size="size-5" />
                        </span>

                        <div class="pt-1">
                            <div class="flex items-baseline gap-3">
                                <span class="font-mono text-xs text-content-subtle tabular-nums">
                                    {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <h3 class="font-display text-xl font-bold">
                                    {{ __("home.process.steps.{$step['key']}.title") }}
                                </h3>
                            </div>

                            <p class="mt-2 text-sm leading-relaxed text-content-muted text-pretty sm:text-base">
                                {{ __("home.process.steps.{$step['key']}.body") }}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </x-ui.container>
    </x-ui.section>
@endif
