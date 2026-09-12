@props(['testimonials'])

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@if ($testimonials->isNotEmpty())
    <x-ui.section id="testimonials" size="compact">
        <div class="glow -top-24 end-1/4 size-[28rem] bg-accent-500/15" aria-hidden="true"></div>

        <x-ui.container class="relative">
            <x-ui.section-heading
                :eyebrow="__('home.testimonials.eyebrow')"
                :title="__('home.testimonials.title')"
                align="center"
            />

            <div
                @class([
                    'mt-12 grid gap-5',
                    'md:grid-cols-2' => $testimonials->count() === 2,
                    'md:grid-cols-2 lg:grid-cols-3' => $testimonials->count() > 2,
                    'max-w-2xl mx-auto' => $testimonials->count() === 1,
                ])
                data-reveal-group
            >
                @foreach ($testimonials as $testimonial)
                    <figure class="flex flex-col rounded-2xl bg-surface-raised p-6 ring-1 ring-hairline">
                        @if ($testimonial->rating)
                            <div class="flex gap-0.5 text-accent-400" role="img" aria-label="{{ trans_choice('home.testimonials.rating', $testimonial->rating) }}">
                                @for ($i = 0; $i < $testimonial->rating; $i++)
                                    <x-ui.icon name="star" size="size-4" />
                                @endfor
                            </div>
                        @endif

                        <blockquote class="mt-4 grow text-content text-pretty">
                            &ldquo;{{ $testimonial->quote }}&rdquo;
                        </blockquote>

                        <figcaption class="mt-6 flex items-center gap-3 border-t border-hairline pt-5">
                            @if ($testimonial->avatar_path)
                                <img
                                    src="{{ Storage::url($testimonial->avatar_path) }}"
                                    alt=""
                                    loading="lazy"
                                    class="size-10 shrink-0 rounded-full object-cover"
                                >
                            @else
                                {{-- Initials rather than a generic silhouette: it
                                     stays personal and never looks like a
                                     placeholder someone forgot to replace. --}}
                                <span class="grid size-10 shrink-0 place-items-center rounded-full bg-brand-gradient font-display text-sm font-bold text-white">
                                    {{ Str::of($testimonial->author_name)->explode(' ')->take(2)->map(fn ($part) => Str::substr($part, 0, 1))->implode('') }}
                                </span>
                            @endif

                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-content">{{ $testimonial->author_name }}</p>
                                <p class="truncate text-xs text-content-subtle">
                                    {{ collect([
                                        (string) $testimonial->author_title,
                                        $testimonial->client?->is_named ? $testimonial->client->name : null,
                                    ])->filter()->implode(' · ') }}
                                </p>
                            </div>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>
@endif
