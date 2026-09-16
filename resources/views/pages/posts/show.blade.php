@php
    use Illuminate\Support\Facades\Storage;

    $minutes = $post->reading_minutes ?: $article->readingMinutes;
    $shareUrl = url()->current();
@endphp

<x-layouts.app
    :title="(string) $post->title"
    :description="(string) $post->excerpt"
    :image="$post->cover_image_path"
    type="article"
>
    <x-slot:head>
        {{-- Article structured data. Search engines and AI assistants read
             this to establish what the piece is and who published it. --}}
        <script type="application/ld+json">
            {!! json_encode(array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => (string) $post->title,
                'description' => (string) $post->excerpt,
                'datePublished' => $post->published_at?->toIso8601String(),
                'dateModified' => $post->updated_at?->toIso8601String(),
                'author' => $post->author_name ? ['@type' => 'Person', 'name' => $post->author_name] : null,
                'publisher' => ['@type' => 'Organization', 'name' => config('site.name')],
                'image' => $post->cover_image_path ? url(\App\Support\Media::url($post->cover_image_path)) : null,
                'mainEntityOfPage' => $shareUrl,
            ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>

        <script type="application/ld+json">
            {!! json_encode(\App\Support\Seo::breadcrumbs([
                config('site.name') => route('home'),
                __('blog.title') => route('posts.index'),
                (string) $post->title => $shareUrl,
            ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    </x-slot:head>

    <article>
        <x-ui.section size="compact">
            <div class="glow -top-32 start-1/3 size-[28rem] bg-brand-500/20" aria-hidden="true"></div>

            <x-ui.container size="narrow" class="relative">
                <nav aria-label="Breadcrumb" class="mb-6">
                    <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 text-sm text-content-subtle transition-colors hover:text-content">
                        <x-ui.icon name="arrow-right" size="size-4" class="-scale-x-100 rtl:scale-x-100" />
                        {{ __('blog.back') }}
                    </a>
                </nav>

                <div class="flex flex-wrap items-center gap-2">
                    @if ($post->category)
                        <x-ui.badge tone="iris">{{ __("blog.categories.{$post->category}") }}</x-ui.badge>
                    @endif
                    <span class="text-sm text-content-subtle">
                        {{ trans_choice('blog.reading_time', $minutes, ['count' => $minutes]) }}
                    </span>
                </div>

                <h1 class="mt-4 font-display text-4xl leading-tight font-bold text-balance sm:text-5xl">
                    {{ $post->title }}
                </h1>

                @if (! $post->excerpt->isEmpty())
                    <p class="mt-5 text-lg leading-relaxed text-content-muted text-pretty">{{ $post->excerpt }}</p>
                @endif

                <p class="mt-6 text-sm text-content-subtle">
                    <time datetime="{{ $post->published_at?->toDateString() }}">
                        {{ $post->published_at?->isoFormat('D MMMM Y') }}
                    </time>
                    @if ($post->author_name)
                        · {{ $post->author_name }}
                    @endif
                </p>

                @if ($post->cover_image_path)
                    {{-- LCP candidate on a post page. --}}
                    <img
                        src="{{ \App\Support\Media::url($post->cover_image_path) }}"
                        alt=""
                        fetchpriority="high"
                        decoding="async"
                        class="mt-10 w-full rounded-2xl object-cover ring-1 ring-hairline"
                    >
                @endif
            </x-ui.container>
        </x-ui.section>

        <x-ui.section size="compact" class="pt-0">
            <x-ui.container>
                <div class="grid gap-10 lg:grid-cols-12">
                    @if ($article->hasTableOfContents())
                        {{-- Only shown from three headings up: one or two is
                             a list, not a table of contents. --}}
                        <aside class="lg:col-span-3 lg:order-last">
                            <nav class="sticky top-24" aria-label="{{ __('blog.contents') }}">
                                <h2 class="text-xs font-semibold tracking-wider text-content-subtle uppercase">
                                    {{ __('blog.contents') }}
                                </h2>
                                <ul class="mt-4 flex flex-col gap-2 border-s border-hairline ps-4">
                                    @foreach ($article->headings as $heading)
                                        <li @class(['ps-3' => $heading['level'] === 3])>
                                            <a href="#{{ $heading['id'] }}" class="text-sm text-content-muted transition-colors hover:text-accent-400">
                                                {{ $heading['text'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </nav>
                        </aside>
                    @endif

                    <div @class(['lg:col-span-9' => $article->hasTableOfContents(), 'lg:col-span-8 lg:col-start-3' => ! $article->hasTableOfContents()])>
                        <div class="prose-content">
                            {!! $article->html !!}
                        </div>

                        {{-- Share --}}
                        <div class="mt-12 flex flex-wrap items-center gap-3 border-t border-hairline pt-8" x-data="{ copied: false }">
                            <span class="text-sm font-medium text-content-subtle">{{ __('blog.share') }}</span>

                            <a
                                href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="{{ __('blog.share_linkedin') }}"
                                class="grid size-10 place-items-center rounded-full text-content-subtle ring-1 ring-hairline transition-colors hover:text-accent-400"
                            >
                                <x-ui.icon name="linkedin" size="size-4" />
                            </a>

                            <a
                                href="https://x.com/intent/post?url={{ urlencode($shareUrl) }}&text={{ urlencode((string) $post->title) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="{{ __('blog.share_x') }}"
                                class="grid size-10 place-items-center rounded-full text-content-subtle ring-1 ring-hairline transition-colors hover:text-accent-400"
                            >
                                <x-ui.icon name="x" size="size-4" />
                            </a>

                            <button
                                type="button"
                                x-on:click="navigator.clipboard.writeText('{{ $shareUrl }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                                class="inline-flex h-10 items-center gap-2 rounded-full px-4 text-sm text-content-subtle ring-1 ring-hairline transition-colors hover:text-accent-400"
                            >
                                <x-ui.icon name="check" size="size-4" x-show="copied" x-cloak />
                                <span x-text="copied ? '{{ __('blog.copied') }}' : '{{ __('blog.copy_link') }}'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </x-ui.container>
        </x-ui.section>
    </article>

    @if ($related->isNotEmpty())
        <x-ui.section tone="raised" size="compact">
            <x-ui.container>
                <h2 class="font-display text-2xl font-bold sm:text-3xl" data-reveal="up">{{ __('blog.related') }}</h2>

                <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3" data-reveal-group>
                    @foreach ($related as $item)
                        <x-ui.post-card :post="$item" />
                    @endforeach
                </div>
            </x-ui.container>
        </x-ui.section>
    @endif

    <x-sections.cta />
</x-layouts.app>
