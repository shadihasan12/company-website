@php
    $estimator = config('site.estimator');
    $budgets = config('site.leads.budget_ranges');
    $timelines = config('site.leads.timelines');
    $steps = ['service', 'platforms', 'features', 'stage', 'timing', 'details'];
@endphp

<x-layouts.app :title="__('estimator.title')" :description="__('estimator.subhead')">
    <x-ui.section size="compact">
        <div class="glow -top-32 start-1/3 size-[30rem] bg-brand-500/25" aria-hidden="true"></div>

        <x-ui.container size="narrow" class="relative">
            <x-ui.section-heading :title="__('estimator.heading')" as="h1" align="center">
                {{ __('estimator.subhead') }}
            </x-ui.section-heading>

            {{--
                The wizard is progressive: every step is present in the DOM
                and only visibility changes, so a submission carries all
                answers and nothing depends on multiple round trips.
                Server-side validation is still the authority.
            --}}
            <form
                method="POST"
                action="{{ route('scope.store') }}"
                class="relative mt-12"
                x-data="{
                    step: 0,
                    total: {{ count($steps) }},
                    sending: false,
                    next() { if (this.step < this.total - 1) this.step++ },
                    back() { if (this.step > 0) this.step-- },
                }"
                x-on:submit="sending = true"
            >
                @csrf
                <x-ui.honeypot />

                {{-- Progress --}}
                <div class="mb-8">
                    <div class="flex items-center justify-between text-sm text-content-subtle">
                        <span x-text="`{{ __('estimator.step_of', ['current' => ':c', 'total' => count($steps)]) }}`.replace(':c', step + 1)"></span>
                        <span x-text="`${Math.round(((step + 1) / total) * 100)}%`"></span>
                    </div>

                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-surface-sunken">
                        <div
                            class="h-full rounded-full bg-brand-gradient transition-[width] duration-300 ease-out-expo"
                            :style="`width: ${((step + 1) / total) * 100}%`"
                        ></div>
                    </div>
                </div>

                <div class="rounded-2xl bg-surface-raised p-6 ring-1 ring-hairline sm:p-8">
                    {{-- 1. Service --}}
                    <fieldset x-show="step === 0">
                        <legend class="font-display text-2xl font-bold">{{ __('estimator.steps.service') }}</legend>

                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            @foreach ($services as $service)
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl bg-surface p-4 ring-1 ring-hairline transition-colors has-checked:ring-2 has-checked:ring-accent-400">
                                    <input type="radio" name="service" value="{{ $service->slug }}" class="accent-accent-500">
                                    <span class="text-sm font-medium">{{ $service->title }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    {{-- 2. Platforms --}}
                    <fieldset x-show="step === 1" x-cloak>
                        <legend class="font-display text-2xl font-bold">{{ __('estimator.steps.platforms') }}</legend>
                        <p class="mt-2 text-sm text-content-subtle">{{ __('estimator.hints.platforms') }}</p>

                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            @foreach ($estimator['platforms'] as $platform)
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl bg-surface p-4 ring-1 ring-hairline transition-colors has-checked:ring-2 has-checked:ring-accent-400">
                                    <input type="checkbox" name="platforms[]" value="{{ $platform }}" class="accent-accent-500">
                                    <span class="text-sm font-medium">{{ __("estimator.platforms.{$platform}") }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    {{-- 3. Features --}}
                    <fieldset x-show="step === 2" x-cloak>
                        <legend class="font-display text-2xl font-bold">{{ __('estimator.steps.features') }}</legend>
                        <p class="mt-2 text-sm text-content-subtle">{{ __('estimator.hints.features') }}</p>

                        <div class="mt-6 grid gap-2 sm:grid-cols-2">
                            @foreach ($estimator['features'] as $feature)
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl bg-surface p-3 ring-1 ring-hairline transition-colors has-checked:ring-2 has-checked:ring-accent-400">
                                    <input type="checkbox" name="features[]" value="{{ $feature }}" class="accent-accent-500">
                                    <span class="text-sm">{{ __("estimator.features.{$feature}") }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    {{-- 4. Stage --}}
                    <fieldset x-show="step === 3" x-cloak>
                        <legend class="font-display text-2xl font-bold">{{ __('estimator.steps.stage') }}</legend>

                        <div class="mt-6 grid gap-3">
                            @foreach ($estimator['stages'] as $stage)
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl bg-surface p-4 ring-1 ring-hairline transition-colors has-checked:ring-2 has-checked:ring-accent-400">
                                    <input type="radio" name="stage" value="{{ $stage }}" class="accent-accent-500">
                                    <span class="text-sm font-medium">{{ __("estimator.stages.{$stage}") }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    {{-- 5. Timing --}}
                    <fieldset x-show="step === 4" x-cloak>
                        <legend class="font-display text-2xl font-bold">{{ __('estimator.steps.timing') }}</legend>
                        <p class="mt-2 text-sm text-content-subtle">{{ __('estimator.hints.timing') }}</p>

                        <div class="mt-6 grid gap-5 sm:grid-cols-2">
                            <x-ui.field
                                name="timeline"
                                :label="__('contact.fields.timeline')"
                                :options="collect($timelines)->mapWithKeys(fn ($key) => [$key => __(\"contact.timeline.{$key}\")])"
                            />
                            <x-ui.field
                                name="budget_range"
                                :label="__('contact.fields.budget_range')"
                                :options="collect($budgets)->mapWithKeys(fn ($key) => [$key => __(\"contact.budget.{$key}\")])"
                            />
                        </div>
                    </fieldset>

                    {{-- 6. Details --}}
                    <fieldset x-show="step === 5" x-cloak>
                        <legend class="font-display text-2xl font-bold">{{ __('estimator.steps.details') }}</legend>

                        <div class="mt-6 flex flex-col gap-5">
                            <div class="grid gap-5 sm:grid-cols-2">
                                <x-ui.field name="name" :label="__('contact.fields.name')" required />
                                <x-ui.field name="email" type="email" :label="__('contact.fields.email')" required />
                                <x-ui.field name="company" :label="__('contact.fields.company')" />
                                <x-ui.field name="phone" type="tel" :label="__('contact.fields.phone')" />
                            </div>

                            <x-ui.field
                                name="message"
                                :label="__('contact.fields.message')"
                                :rows="4"
                                :placeholder="__('contact.placeholders.message')"
                            />

                            <x-ui.turnstile />
                        </div>
                    </fieldset>
                </div>

                @if ($errors->any())
                    <div role="alert" class="mt-5 rounded-xl bg-red-500/10 p-4 ring-1 ring-red-500/30">
                        <p class="text-sm font-semibold text-red-400">{{ __('contact.errors.heading') }}</p>
                        <ul class="mt-2 flex flex-col gap-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm text-red-400">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-6 flex items-center justify-between gap-3">
                    <x-ui.button
                        type="button"
                        variant="ghost"
                        x-on:click="back()"
                        x-bind:disabled="step === 0"
                    >
                        {{ __('estimator.back') }}
                    </x-ui.button>

                    <x-ui.button
                        type="button"
                        size="lg"
                        icon="arrow-right"
                        x-show="step < total - 1"
                        x-on:click="next()"
                    >
                        {{ __('estimator.next') }}
                    </x-ui.button>

                    <x-ui.button
                        type="submit"
                        size="lg"
                        icon="arrow-right"
                        x-show="step === total - 1"
                        x-cloak
                        x-bind:disabled="sending"
                    >
                        <span x-show="! sending">{{ __('estimator.submit') }}</span>
                        <span x-show="sending" x-cloak>{{ __('contact.sending') }}</span>
                    </x-ui.button>
                </div>
            </form>
        </x-ui.container>
    </x-ui.section>
</x-layouts.app>
