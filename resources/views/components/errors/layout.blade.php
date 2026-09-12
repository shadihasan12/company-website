@props(['code', 'title', 'body'])

<x-layouts.app :title="$title" noindex>
    <x-ui.section size="tall">
        <div class="glow -top-24 start-1/3 size-[30rem] bg-brand-500/20" aria-hidden="true"></div>

        <x-ui.container class="relative">
            <div class="mx-auto flex max-w-xl flex-col items-center gap-6 text-center">
                <p class="font-display text-7xl font-bold text-brand-gradient sm:text-8xl">{{ $code }}</p>

                <h1 class="font-display text-3xl font-bold text-balance sm:text-4xl">{{ $title }}</h1>

                <p class="text-lg text-content-muted text-pretty">{{ $body }}</p>

                <div class="flex flex-wrap justify-center gap-3">
                    <x-ui.button :href="route('home')" size="lg" icon="arrow-right">
                        {{ __('errors.home') }}
                    </x-ui.button>
                    <x-ui.button :href="route('work.index')" variant="secondary" size="lg">
                        {{ __('errors.work') }}
                    </x-ui.button>
                </div>
            </div>
        </x-ui.container>
    </x-ui.section>
</x-layouts.app>
