@props(['faqs', 'title' => null])

@php
    $faqs = collect($faqs)->filter(fn ($faq) => filled($faq['question'] ?? null) && filled($faq['answer'] ?? null));
@endphp

@if ($faqs->isNotEmpty())
    <div {{ $attributes }}>
        @if ($title)
            <h2 class="font-display text-2xl font-bold sm:text-3xl">{{ $title }}</h2>
        @endif

        <div class="mt-8 divide-y divide-hairline border-y border-hairline">
            @foreach ($faqs as $index => $faq)
                <div x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }">
                    <h3>
                        <button
                            type="button"
                            x-on:click="open = !open"
                            :aria-expanded="open ? 'true' : 'false'"
                            aria-controls="faq-{{ $index }}"
                            class="flex w-full items-center justify-between gap-6 py-5 text-start"
                        >
                            <span class="font-medium text-content">{{ $faq['question'] }}</span>
                            <x-ui.icon
                                name="chevron-down"
                                size="size-5"
                                class="shrink-0 text-content-subtle transition-transform duration-200"
                                ::class="open && 'rotate-180'"
                            />
                        </button>
                    </h3>

                    {{-- x-collapse animates height rather than toggling
                         display, so the answer is still in the DOM and
                         reachable by search engines and find-in-page. --}}
                    <div id="faq-{{ $index }}" x-show="open" x-collapse>
                        <p class="pb-5 text-content-muted text-pretty">{{ $faq['answer'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- FAQPage structured data. In 2026 this is how search engines and AI
         assistants read the answers, so it matters as much as the markup. --}}
    @push('schema')
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $faqs->map(fn ($faq) => [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
                ])->values(),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endpush
@endif
