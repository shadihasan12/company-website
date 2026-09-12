<x-layouts.app :title="__('contact.thanks.title')">
    <x-ui.section size="tall">
        <div class="glow -top-24 start-1/3 size-[30rem] bg-accent-500/20" aria-hidden="true"></div>

        <x-ui.container class="relative">
            <div class="mx-auto flex max-w-xl flex-col items-center gap-6 text-center">
                <span class="grid size-16 place-items-center rounded-2xl bg-brand-gradient text-white">
                    <x-ui.icon name="check" size="size-8" />
                </span>

                <h1 class="font-display text-4xl font-bold text-balance sm:text-5xl">{{ __('contact.thanks.heading') }}</h1>

                <p class="text-lg text-content-muted text-pretty">{{ __('contact.thanks.body') }}</p>

                <div class="flex flex-wrap justify-center gap-3">
                    <x-ui.button :href="route('work.index')" size="lg" icon="arrow-right">
                        {{ __('contact.thanks.work') }}
                    </x-ui.button>
                    <x-ui.button :href="route('home')" variant="secondary" size="lg">
                        {{ __('contact.thanks.back') }}
                    </x-ui.button>
                </div>
            </div>
        </x-ui.container>
    </x-ui.section>
</x-layouts.app>
