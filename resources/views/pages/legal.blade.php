@php
    $replacements = [
        'company' => config('site.legal_name'),
        'email' => config('site.contact.email'),
    ];

    $title = __("legal.{$document}.title");
    $intro = __("legal.{$document}.intro", $replacements);
    $sections = __("legal.{$document}.sections");
@endphp

<x-layouts.app :title="$title" :description="$intro">
    <x-ui.section size="compact">
        <x-ui.container size="narrow">
            <h1 class="font-display text-4xl font-bold text-balance sm:text-5xl">{{ $title }}</h1>

            <p class="mt-3 text-sm text-content-subtle">
                {{ __('legal.updated') }}: {{ $updatedAt->isoFormat('D MMMM Y') }}
            </p>

            <p class="mt-6 text-lg leading-relaxed text-content-muted text-pretty">{{ $intro }}</p>

            <div class="mt-12 flex flex-col gap-10">
                @foreach ($sections as $section)
                    <section>
                        <h2 class="font-display text-xl font-bold sm:text-2xl">{{ $section['heading'] }}</h2>
                        <p class="mt-3 leading-relaxed text-content-muted text-pretty">
                            {{ __($section['body'], $replacements) }}
                        </p>
                    </section>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>
</x-layouts.app>
