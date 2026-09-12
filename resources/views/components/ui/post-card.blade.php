@props(['post'])

@php
    use Illuminate\Support\Facades\Storage;

    $minutes = $post->reading_minutes ?: \App\Support\Article::readingMinutes((string) $post->body);
@endphp

<x-ui.card :href="route('posts.show', $post->slug)" class="p-5">
    <div class="relative aspect-16/10 overflow-hidden rounded-xl bg-surface-sunken">
        @if ($post->cover_image_path)
            <img
                src="{{ Storage::url($post->cover_image_path) }}"
                alt=""
                loading="lazy"
                decoding="async"
                class="size-full object-cover transition-transform duration-500 ease-out-expo group-hover:scale-[1.04]"
            >
        @else
            <div class="absolute inset-0 bg-brand-gradient opacity-80"></div>
        @endif
    </div>

    <div class="mt-5 flex flex-wrap items-center gap-2">
        @if ($post->category)
            <x-ui.badge tone="iris">{{ __("blog.categories.{$post->category}") }}</x-ui.badge>
        @endif

        <span class="text-xs text-content-subtle">
            {{ trans_choice('blog.reading_time', $minutes, ['count' => $minutes]) }}
        </span>
    </div>

    <h3 class="mt-3 font-display text-lg leading-snug font-bold text-balance">{{ $post->title }}</h3>

    <p class="mt-2 grow text-sm leading-relaxed text-content-muted">
        {{ Str::limit((string) $post->excerpt, 120) }}
    </p>

    <p class="mt-5 text-xs text-content-subtle">
        <time datetime="{{ $post->published_at?->toDateString() }}">
            {{ $post->published_at?->isoFormat('D MMMM Y') }}
        </time>
        @if ($post->author_name)
            · {{ $post->author_name }}
        @endif
    </p>
</x-ui.card>
