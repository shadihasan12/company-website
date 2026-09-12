@php
    $contact = config('site.contact');

    $serviceOptions = $services->mapWithKeys(fn ($service) => [$service->slug => (string) $service->title]);
    $budgetOptions = collect(config('site.leads.budget_ranges'))->mapWithKeys(fn ($key) => [$key => __("contact.budget.{$key}")]);
    $timelineOptions = collect(config('site.leads.timelines'))->mapWithKeys(fn ($key) => [$key => __("contact.timeline.{$key}")]);
@endphp

<x-layouts.app :title="__('contact.title')" :description="__('contact.subhead')">
    <x-ui.section size="compact">
        <div class="glow -top-32 start-1/4 size-[30rem] bg-iris-500/25" aria-hidden="true"></div>

        <x-ui.container class="relative">
            <div class="grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <h1 class="font-display text-4xl leading-tight font-bold text-balance sm:text-5xl">
                        {{ __('contact.heading') }}
                    </h1>

                    <p class="mt-4 text-lg leading-relaxed text-content-muted text-pretty">
                        {{ __('contact.subhead') }}
                    </p>

                    @if ($errors->any())
                        <div role="alert" class="mt-8 rounded-xl bg-red-500/10 p-4 ring-1 ring-red-500/30">
                            <p class="text-sm font-semibold text-red-400">{{ __('contact.errors.heading') }}</p>
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('contact.store') }}"
                        class="relative mt-8 flex flex-col gap-5"
                        x-data="{ sending: false }"
                        x-on:submit="sending = true"
                    >
                        @csrf
                        <x-ui.honeypot />

                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-ui.field name="name" :label="__('contact.fields.name')" required />
                            <x-ui.field name="email" type="email" :label="__('contact.fields.email')" required />
                            <x-ui.field name="company" :label="__('contact.fields.company')" />
                            <x-ui.field name="phone" type="tel" :label="__('contact.fields.phone')" />
                        </div>

                        <x-ui.field
                            name="service"
                            :label="__('contact.fields.service')"
                            :options="$serviceOptions"
                            :placeholder="$selectedService"
                        />

                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-ui.field name="budget_range" :label="__('contact.fields.budget_range')" :options="$budgetOptions" />
                            <x-ui.field name="timeline" :label="__('contact.fields.timeline')" :options="$timelineOptions" />
                        </div>

                        <x-ui.field
                            name="message"
                            :label="__('contact.fields.message')"
                            :rows="6"
                            :placeholder="__('contact.placeholders.message')"
                            required
                        />

                        <x-ui.turnstile />

                        <div class="flex flex-wrap items-center gap-4">
                            <x-ui.button type="submit" size="lg" icon="arrow-right" x-bind:disabled="sending">
                                <span x-show="! sending">{{ __('contact.submit') }}</span>
                                <span x-show="sending" x-cloak>{{ __('contact.sending') }}</span>
                            </x-ui.button>

                            <p class="text-sm text-content-subtle">{{ __('contact.aside.response') }}</p>
                        </div>
                    </form>
                </div>

                {{-- Alternatives to the form. Not everyone wants to fill one
                     in, and a lost enquiry costs more than a duplicated
                     contact route. --}}
                <aside class="lg:col-span-5">
                    <div class="glass sticky top-24 rounded-2xl p-6">
                        <h2 class="font-display text-lg font-bold">{{ __('contact.aside.title') }}</h2>

                        <ul class="mt-5 flex flex-col gap-4">
                            <li>
                                <a href="mailto:{{ $contact['email'] }}" class="group flex items-center gap-3 text-content-muted transition-colors hover:text-accent-400">
                                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-brand-500/12 text-brand-300 transition-colors group-hover:bg-accent-500/15 group-hover:text-accent-400">
                                        <x-ui.icon name="mail" size="size-5" />
                                    </span>
                                    <span class="text-sm">{{ $contact['email'] }}</span>
                                </a>
                            </li>

                            <li>
                                <a href="tel:{{ preg_replace('/\s+/', '', $contact['phone']) }}" dir="ltr" class="group flex items-center gap-3 text-content-muted transition-colors hover:text-accent-400">
                                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-brand-500/12 text-brand-300 transition-colors group-hover:bg-accent-500/15 group-hover:text-accent-400">
                                        <x-ui.icon name="phone" size="size-5" />
                                    </span>
                                    <span class="text-sm">{{ $contact['phone'] }}</span>
                                </a>
                            </li>

                            @if ($contact['whatsapp'])
                                <li>
                                    <a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-3 text-content-muted transition-colors hover:text-accent-400">
                                        <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-brand-500/12 text-brand-300 transition-colors group-hover:bg-accent-500/15 group-hover:text-accent-400">
                                            <x-ui.icon name="whatsapp" size="size-5" />
                                        </span>
                                        <span class="text-sm">{{ __('common.cta_whatsapp') }}</span>
                                    </a>
                                </li>
                            @endif

                            @if ($contact['booking_url'])
                                <li>
                                    <a href="{{ $contact['booking_url'] }}" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-3 text-content-muted transition-colors hover:text-accent-400">
                                        <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-brand-500/12 text-brand-300 transition-colors group-hover:bg-accent-500/15 group-hover:text-accent-400">
                                            <x-ui.icon name="rocket" size="size-5" />
                                        </span>
                                        <span class="text-sm">{{ __('common.cta_call') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </aside>
            </div>
        </x-ui.container>
    </x-ui.section>
</x-layouts.app>
