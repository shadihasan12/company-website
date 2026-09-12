@php
    use App\Support\Nav;

    $contact = config('site.contact');
@endphp

<section class="relative isolate overflow-hidden">
    <x-ui.container class="py-20 sm:py-28">
        <div class="ring-gradient relative overflow-hidden rounded-3xl bg-surface-raised px-6 py-16 text-center sm:px-12 sm:py-20">
            {{-- Same orbit language as the hero, so the page opens and
                 closes on the brand rather than trailing off. --}}
            <div class="glow -top-24 start-1/4 size-[24rem] bg-iris-500/25" aria-hidden="true"></div>
            <div class="glow -bottom-24 end-1/4 size-[22rem] bg-accent-500/25" aria-hidden="true"></div>

            <div class="relative mx-auto flex max-w-2xl flex-col items-center gap-6">
                <h2 class="font-display text-3xl leading-tight font-bold text-balance sm:text-5xl" data-reveal="up">
                    {{ __('home.cta.title') }}
                </h2>

                <p class="text-lg text-content-muted text-pretty" data-reveal="up">
                    {{ __('home.cta.subhead') }}
                </p>

                <div class="flex flex-wrap items-center justify-center gap-3" data-reveal="up">
                    <x-ui.button :href="Nav::contact()" size="lg" icon="arrow-right">
                        {{ __('home.cta.primary') }}
                    </x-ui.button>

                    @if ($contact['whatsapp'])
                        <x-ui.button
                            href="https://wa.me/{{ $contact['whatsapp'] }}"
                            variant="secondary"
                            size="lg"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <x-ui.icon name="whatsapp" size="size-4" />
                            {{ __('common.cta_whatsapp') }}
                        </x-ui.button>
                    @endif
                </div>

                <p class="text-sm text-content-subtle" data-reveal="up">
                    {{ __('home.cta.reassurance') }}
                </p>
            </div>
        </div>
    </x-ui.container>
</section>
