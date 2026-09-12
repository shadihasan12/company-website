<x-layouts.app :description="__('home.subhead')">
    <x-ui.section size="tall" class="text-center">
        {{-- Ambient brand glows. Decorative, pointer-inert, and positioned
             with logical properties so they mirror correctly in Arabic. --}}
        <div class="glow -top-24 start-1/4 size-[32rem] bg-iris-500/30" aria-hidden="true"></div>
        <div class="glow top-40 end-1/4 size-[28rem] bg-accent-500/20" aria-hidden="true"></div>

        <x-ui.container class="relative">
            <div class="mx-auto flex max-w-3xl flex-col items-center gap-7">
                <x-ui.badge tone="accent" data-reveal="up">
                    {{ __('home.eyebrow') }}
                </x-ui.badge>

                <h1
                    class="font-display text-4xl leading-[1.1] font-bold text-balance sm:text-6xl lg:text-7xl"
                    data-reveal="up"
                >
                    {{ __('home.headline') }}
                </h1>

                <p class="max-w-2xl text-lg leading-relaxed text-content-muted text-pretty sm:text-xl" data-reveal="up">
                    {{ __('home.subhead') }}
                </p>

                <div class="flex flex-wrap items-center justify-center gap-3" data-reveal="up">
                    <x-ui.button href="{{ route('styleguide') }}" size="lg" icon="arrow-right">
                        {{ __('home.view_styleguide') }}
                    </x-ui.button>

                    <x-ui.button href="mailto:{{ config('site.contact.email') }}" variant="secondary" size="lg">
                        {{ __('common.get_in_touch') }}
                    </x-ui.button>
                </div>

                <p class="text-xs text-content-subtle" data-reveal="up">
                    {{ __('home.shell_note') }}
                </p>
            </div>
        </x-ui.container>
    </x-ui.section>
</x-layouts.app>
