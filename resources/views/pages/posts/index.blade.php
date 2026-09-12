@php
    $categoryOptions = collect($categories)->map(fn (string $slug) => (object) [
        'slug' => $slug,
        'name' => __("blog.categories.{$slug}"),
    ]);
@endphp

<x-layouts.app :title="__('blog.title')" :description="__('blog.subhead')">
    <x-ui.section size="compact">
        <div class="glow -top-32 end-1/4 size-[30rem] bg-iris-500/25" aria-hidden="true"></div>

        <x-ui.container class="relative">
            <x-ui.section-heading
                :eyebrow="__('nav.insights')"
                :title="__('blog.heading')"
                as="h1"
                align="center"
            >
                {{ __('blog.subhead') }}
            </x-ui.section-heading>

            @if ($categoryOptions->isNotEmpty())
                <x-ui.filter-bar
                    class="mt-12 justify-center"
                    param="category"
                    :options="$categoryOptions"
                    :active="$category"
                />
            @endif

            @if ($posts->isEmpty())
                <div class="mt-12 rounded-2xl bg-surface-raised p-14 text-center ring-1 ring-hairline">
                    <p class="text-lg text-content-muted">
                        {{ $category ? __('blog.empty_filtered') : __('blog.empty') }}
                    </p>
                </div>
            @else
                <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3" data-reveal-group>
                    @foreach ($posts as $post)
                        <x-ui.post-card :post="$post" />
                    @endforeach
                </div>

                @if ($posts->hasPages())
                    <div class="mt-12">{{ $posts->links() }}</div>
                @endif
            @endif
        </x-ui.container>
    </x-ui.section>

    <x-sections.cta />
</x-layouts.app>
