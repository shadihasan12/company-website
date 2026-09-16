@props(['images', 'title' => null])

@php
    use App\Support\Media;

    $images = collect($images)->filter();
    $urls = $images->map(fn (string $path) => Media::url($path))->values();
@endphp

@if ($images->isNotEmpty())
    <div
        x-data="lightbox({{ $urls->toJson() }})"
        x-on:keydown.escape.window="close()"
        x-on:keydown.arrow-right.window="next()"
        x-on:keydown.arrow-left.window="previous()"
        {{ $attributes }}
    >
        @if ($title)
            <h2 class="font-display text-2xl font-bold sm:text-3xl">{{ $title }}</h2>
        @endif

        <ul @class([
            'grid gap-4',
            'mt-8' => $title,
            'sm:grid-cols-2 lg:grid-cols-3' => $images->count() > 1,
            // A lone screenshot stretched to the full container is taller
            // than the viewport; hold it to one column's width instead.
            'mx-auto max-w-lg' => $images->count() === 1,
        ])>
            @foreach ($urls as $index => $url)
                <li>
                    <button
                        type="button"
                        x-on:click="open({{ $index }})"
                        class="group block w-full overflow-hidden rounded-xl bg-surface-sunken p-3 ring-1 ring-hairline transition-[box-shadow] duration-300 hover:ring-brand-400/40"
                    >
                        {{-- These are app screenshots, mostly portrait. Cropped
                             to fill a landscape tile they showed a slice of one
                             screen and nothing legible, so the tile is portrait
                             and the shot is contained inside it whole. --}}
                        <img
                            src="{{ $url }}"
                            alt="{{ __('work.gallery.screenshot', ['number' => $index + 1]) }}"
                            loading="lazy"
                            decoding="async"
                            class="aspect-3/4 w-full rounded-lg object-contain transition-transform duration-500 ease-out-expo group-hover:scale-[1.03]"
                        >
                    </button>
                </li>
            @endforeach
        </ul>

        {{-- Lightbox --}}
        <div
            x-show="isOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-100 flex items-center justify-center bg-ink-950/90 p-4 backdrop-blur-sm"
            role="dialog"
            aria-modal="true"
            :aria-label="'{{ __('work.gallery.viewer') }}'"
            x-on:click.self="close()"
        >
            <button
                type="button"
                x-on:click="close()"
                class="absolute top-4 end-4 grid size-11 place-items-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20"
                aria-label="{{ __('nav.close_menu') }}"
            >
                <x-ui.icon name="close" />
            </button>

            <template x-if="images.length > 1">
                <button
                    type="button"
                    x-on:click.stop="previous()"
                    class="absolute start-4 grid size-11 place-items-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20"
                    aria-label="{{ __('common.previous') }}"
                >
                    <x-ui.icon name="arrow-right" class="-scale-x-100 rtl:scale-x-100" />
                </button>
            </template>

            <img
                :src="images[current]"
                :alt="'{{ __('work.gallery.viewer') }}'"
                class="max-h-[85dvh] max-w-full rounded-xl object-contain shadow-2xl"
            >

            <template x-if="images.length > 1">
                <button
                    type="button"
                    x-on:click.stop="next()"
                    class="absolute end-4 grid size-11 place-items-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20"
                    aria-label="{{ __('common.next') }}"
                >
                    <x-ui.icon name="arrow-right" class="rtl:-scale-x-100" />
                </button>
            </template>

            <p
                x-show="images.length > 1"
                class="absolute bottom-5 start-1/2 -translate-x-1/2 rounded-full bg-white/10 px-3 py-1 text-sm text-white rtl:translate-x-1/2"
                x-text="`${current + 1} / ${images.length}`"
            ></p>
        </div>
    </div>
@endif
