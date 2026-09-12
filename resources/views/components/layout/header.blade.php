@php
    use App\Support\Nav;

    $primaryNav = collect([
        ['label' => __('nav.work'), 'route' => 'work.index'],
        ['label' => __('nav.about'), 'route' => 'about'],
        ['label' => __('nav.insights'), 'route' => 'posts.index', 'when' => Nav::hasPosts()],
    ])->filter(fn (array $item) => $item['when'] ?? true)->all();

    $services = Nav::services();
@endphp

<a
    href="#main"
    class="sr-only rounded-full bg-accent-500 px-5 py-2.5 text-sm font-medium text-white focus:not-sr-only focus:absolute focus:top-4 focus:start-4 focus:z-100"
>
    {{ __('nav.skip_to_content') }}
</a>

<header
    x-data="{ scrolled: false, mobileOpen: false, servicesOpen: false }"
    x-on:scroll.window="scrolled = window.scrollY > 8"
    x-on:keydown.escape.window="mobileOpen = false; servicesOpen = false"
    class="sticky top-0 z-50 transition-colors duration-300"
    :class="scrolled || mobileOpen ? 'glass' : 'bg-transparent'"
>
    <x-ui.container>
        <div class="flex h-18 items-center justify-between gap-6">
            <a href="{{ route('home') }}" class="shrink-0" aria-label="{{ config('site.name') }}">
                <x-ui.logo />
            </a>

            {{-- Desktop navigation --}}
            <nav class="hidden items-center gap-1 lg:flex" aria-label="{{ __('nav.main_navigation') }}">
                <div
                    class="relative"
                    x-on:mouseenter="servicesOpen = true"
                    x-on:mouseleave="servicesOpen = false"
                >
                    <button
                        type="button"
                        x-on:click="servicesOpen = !servicesOpen"
                        :aria-expanded="servicesOpen ? 'true' : 'false'"
                        aria-controls="services-menu"
                        class="inline-flex h-10 items-center gap-1.5 rounded-full px-4 text-sm font-medium text-content-muted transition-colors hover:bg-surface-raised hover:text-content"
                    >
                        {{ __('nav.services') }}
                        <x-ui.icon
                            name="chevron-down"
                            size="size-4"
                            class="transition-transform duration-200"
                            ::class="servicesOpen && 'rotate-180'"
                        />
                    </button>

                    <div
                        id="services-menu"
                        x-show="servicesOpen"
                        x-cloak
                        x-transition.opacity.duration.150ms
                        class="absolute top-full start-0 z-50 pt-3"
                    >
                        <div class="glass w-[34rem] rounded-2xl p-3 shadow-2xl shadow-ink-950/40">
                            <div class="grid grid-cols-2 gap-1">
                                @foreach ($services as $service)
                                    <a
                                        href="{{ Nav::link('services.show', '#', $service->slug) }}"
                                        class="group flex items-start gap-3 rounded-xl p-3 transition-colors hover:bg-surface-raised"
                                    >
                                        <span class="mt-0.5 grid size-9 shrink-0 place-items-center rounded-lg bg-brand-500/12 text-brand-300 transition-colors group-hover:bg-accent-500/15 group-hover:text-accent-400">
                                            <x-ui.icon :name="$service->icon" size="size-5" />
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block text-sm font-medium text-content">
                                                {{ $service->title }}
                                            </span>
                                            <span class="block text-xs text-content-subtle">
                                                {{ $service->tagline }}
                                            </span>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                @foreach ($primaryNav as $item)
                    <a
                        href="{{ Nav::link($item['route']) }}"
                        class="inline-flex h-10 items-center rounded-full px-4 text-sm font-medium text-content-muted transition-colors hover:bg-surface-raised hover:text-content"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Desktop utilities --}}
            <div class="hidden items-center gap-1 lg:flex">
                <x-ui.language-switcher />
                <x-ui.theme-toggle />
                <x-ui.button :href="Nav::start()" size="sm" class="ms-2">
                    {{ __('common.cta_primary') }}
                </x-ui.button>
            </div>

            {{-- Mobile utilities --}}
            <div class="flex items-center gap-1 lg:hidden">
                <x-ui.theme-toggle />
                <button
                    type="button"
                    x-on:click="mobileOpen = !mobileOpen"
                    :aria-expanded="mobileOpen ? 'true' : 'false'"
                    aria-controls="mobile-menu"
                    class="grid size-10 place-items-center rounded-full text-content transition-colors hover:bg-surface-raised"
                >
                    <span class="sr-only" x-text="mobileOpen ? '{{ __('nav.close_menu') }}' : '{{ __('nav.open_menu') }}'"></span>
                    <x-ui.icon name="menu" x-show="! mobileOpen" />
                    <x-ui.icon name="close" x-show="mobileOpen" x-cloak />
                </button>
            </div>
        </div>
    </x-ui.container>

    {{-- Mobile panel --}}
    <div
        id="mobile-menu"
        x-show="mobileOpen"
        x-cloak
        x-collapse
        class="border-t border-hairline lg:hidden"
    >
        <x-ui.container class="py-6">
            <nav class="flex flex-col gap-1" aria-label="{{ __('nav.main_navigation') }}">
                <p class="px-3 pb-2 text-xs font-semibold tracking-wider text-content-subtle uppercase">
                    {{ __('nav.services') }}
                </p>

                @foreach ($services as $service)
                    <a
                        href="{{ Nav::link('services.show', '#', $service->slug) }}"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-content-muted transition-colors hover:bg-surface-raised hover:text-content"
                    >
                        <x-ui.icon :name="$service->icon" size="size-5" class="text-brand-300" />
                        <span class="text-sm font-medium">{{ $service->title }}</span>
                    </a>
                @endforeach

                <hr class="my-3 border-hairline">

                @foreach ($primaryNav as $item)
                    <a
                        href="{{ Nav::link($item['route']) }}"
                        class="rounded-xl px-3 py-2.5 text-sm font-medium text-content-muted transition-colors hover:bg-surface-raised hover:text-content"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach

                <div class="mt-4 flex items-center justify-between gap-3">
                    <x-ui.language-switcher />
                    <x-ui.button :href="Nav::start()" size="sm" class="grow">
                        {{ __('common.cta_primary') }}
                    </x-ui.button>
                </div>
            </nav>
        </x-ui.container>
    </div>
</header>
