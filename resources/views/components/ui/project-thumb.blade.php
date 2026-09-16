@props(['project'])

@php
    use Illuminate\Support\Facades\Storage;

    $image = $project->hero_image_path
        ?? ($project->gallery[0] ?? null);
@endphp

<div {{ $attributes->class(['relative aspect-16/10 overflow-hidden rounded-xl bg-surface-sunken']) }}>
    @if ($image)
        <img
            src="{{ \App\Support\Media::url($image) }}"
            alt="{{ $project->name }}"
            loading="lazy"
            decoding="async"
            class="size-full object-cover transition-transform duration-500 ease-out-expo group-hover:scale-[1.04]"
        >
    @else
        {{-- No screenshot yet. Rather than an empty box or a stock image,
             this renders a branded panel using the logo's orbit motif, so
             the layout reads as intentional while the real artwork is
             still outstanding. --}}
        <div class="absolute inset-0 bg-brand-gradient opacity-90"></div>

        <svg
            class="absolute start-1/2 top-1/2 size-[130%] -translate-x-1/2 -translate-y-1/2 text-white/25 rtl:translate-x-1/2"
            viewBox="0 0 200 200"
            fill="none"
            aria-hidden="true"
        >
            <ellipse cx="100" cy="100" rx="92" ry="42" stroke="currentColor" transform="rotate(-28 100 100)" />
            <ellipse cx="100" cy="100" rx="70" ry="70" stroke="currentColor" />
            <ellipse cx="100" cy="100" rx="42" ry="92" stroke="currentColor" transform="rotate(-28 100 100)" />
        </svg>

        <span class="absolute inset-0 grid place-items-center px-6 text-center font-display text-xl font-bold text-balance text-white/90">
            {{ $project->name }}
        </span>
    @endif
</div>
