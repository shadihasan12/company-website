@props(['clients'])

@if ($clients->isNotEmpty())
    <section class="border-y border-hairline bg-surface-raised py-10" aria-label="{{ __('home.clients.title') }}">
        <p class="mb-8 text-center text-xs font-semibold tracking-[0.2em] text-content-subtle uppercase">
            {{ __('home.clients.title') }}
        </p>

        <div class="marquee-viewport">
            {{-- The list is rendered twice so the CSS loop can translate a
                 clean -50% and land on an identical frame. The duplicate is
                 hidden from assistive technology to avoid reading every
                 client name twice. --}}
            <div class="marquee gap-12 sm:gap-16" style="--marquee-duration: {{ max(24, $clients->count() * 6) }}s">
                @foreach ([false, true] as $isDuplicate)
                    <ul
                        class="flex shrink-0 items-center gap-12 pe-12 sm:gap-16 sm:pe-16"
                        @if ($isDuplicate) aria-hidden="true" @endif
                    >
                        @foreach ($clients as $client)
                            <li class="shrink-0">
                                @php
                                    $logo = \App\Support\Media::url($client->logo_path);
                                @endphp

                                <x-dynamic-component
                                    :component="$client->website_url ? 'ui.external-link' : 'ui.plain'"
                                    :href="$client->website_url"
                                >
                                    @if ($logo)
                                        <img
                                            src="{{ $logo }}"
                                            alt="{{ $client->display_name }}"
                                            loading="lazy"
                                            class="h-8 w-auto opacity-60 grayscale transition duration-300 hover:opacity-100 hover:grayscale-0 sm:h-9"
                                        >
                                    @else
                                        {{-- Until a logo file is uploaded the client is shown
                                             as a wordmark, so the strip is useful now and
                                             upgrades itself when the artwork arrives. --}}
                                        <span class="font-display text-lg font-bold whitespace-nowrap text-content-subtle transition-colors duration-300 hover:text-content sm:text-xl">
                                            {{ $client->display_name }}
                                        </span>
                                    @endif
                                </x-dynamic-component>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </div>
    </section>
@endif
