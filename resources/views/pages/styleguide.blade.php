@php
    use App\Support\Locale;

    $shades = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950];

    $scales = [
        'accent' => 'Accent — magenta. Primary calls to action only.',
        'brand' => 'Brand — violet. Surfaces, icon chips, gradient midpoint.',
        'iris' => 'Iris — indigo. Secondary accent, links, focus.',
        'ink' => 'Ink — navy-tinted neutrals. Every grey on the site.',
    ];

    $sourceColors = [
        'brand-magenta' => '#f01fe0',
        'brand-iris' => '#4b48bc',
        'brand-violet' => '#7149b8',
        'brand-navy' => '#2a2560',
    ];

    $semantic = [
        'surface' => 'Page background',
        'surface-raised' => 'Cards, header glass, inputs',
        'surface-sunken' => 'Footer, recessed panels',
        'content' => 'Primary text',
        'content-muted' => 'Body copy, secondary text',
        'content-subtle' => 'Captions, metadata, placeholders',
    ];

    $buttonVariants = ['primary', 'gradient', 'secondary', 'outline', 'ghost'];
    $iconNames = [
        'device-mobile', 'globe', 'chart-bar', 'puzzle', 'building', 'sparkles', 'server',
        'arrow-right', 'arrow-up-right', 'chevron-down', 'menu', 'close', 'check',
        'sun', 'moon', 'globe-alt', 'mail', 'phone', 'map-pin',
    ];
    $brandIcons = ['linkedin', 'github', 'x', 'instagram', 'facebook', 'youtube', 'whatsapp'];
@endphp

<x-layouts.app title="Styleguide">
    {{-- Internal reference page. Copy here is intentionally English-only in
         both locales — it documents the system for the build team, and is
         not part of the public site. --}}

    <x-ui.section size="compact">
        <x-ui.container>
            <div class="flex flex-col gap-4">
                <x-ui.badge tone="brand">Internal — T0.1</x-ui.badge>
                <h1 class="font-display text-4xl font-bold sm:text-5xl">Design system</h1>
                <p class="max-w-2xl text-lg text-content-muted">
                    Every token, component and motion rule the site is built from.
                    Toggle the theme and switch to Arabic in the header to check both modes and both directions.
                </p>
                <p class="max-w-2xl rounded-xl bg-accent-500/10 p-4 text-sm text-accent-300 ring-1 ring-accent-500/20">
                    <strong class="font-semibold">Colours are approximated</strong> from the supplied palette image.
                    Replace the four source values in <code class="font-mono text-xs">resources/css/app.css</code>
                    when the exact brand hexes arrive — the scales below are derived from them.
                </p>
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- ------------------------------------------------------------------ --}}
    <x-ui.section tone="raised" size="compact">
        <x-ui.container>
            <x-ui.section-heading eyebrow="Colour" title="Brand source colours">
                The four values taken from your palette. Everything else is derived.
            </x-ui.section-heading>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-reveal-group>
                @foreach ($sourceColors as $token => $hex)
                    <div class="overflow-hidden rounded-2xl ring-1 ring-hairline">
                        <div class="h-28" style="background-color: var(--color-{{ $token }})"></div>
                        <div class="bg-surface p-4">
                            <p class="font-mono text-xs text-content">--color-{{ $token }}</p>
                            <p class="mt-1 font-mono text-xs text-content-subtle uppercase">{{ $hex }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- ------------------------------------------------------------------ --}}
    <x-ui.section size="compact">
        <x-ui.container>
            <x-ui.section-heading eyebrow="Colour" title="Scales">
                Eleven steps per family. Use 400–600 for interactive elements and 800–950 for dark surfaces.
            </x-ui.section-heading>

            <div class="mt-10 flex flex-col gap-8">
                @foreach ($scales as $scale => $note)
                    <div>
                        <div class="mb-3 flex flex-wrap items-baseline gap-x-3 gap-y-1">
                            <h3 class="font-display text-lg font-bold capitalize">{{ $scale }}</h3>
                            <p class="text-sm text-content-subtle">{{ $note }}</p>
                        </div>
                        <div class="flex overflow-hidden rounded-xl ring-1 ring-hairline">
                            @foreach ($shades as $shade)
                                <div class="group relative flex-1" title="--color-{{ $scale }}-{{ $shade }}">
                                    <div class="h-16" style="background-color: var(--color-{{ $scale }}-{{ $shade }})"></div>
                                    <p class="bg-surface py-1.5 text-center font-mono text-[10px] text-content-subtle">{{ $shade }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- ------------------------------------------------------------------ --}}
    <x-ui.section tone="raised" size="compact">
        <x-ui.container>
            <x-ui.section-heading eyebrow="Colour" title="Semantic tokens">
                These swap automatically with the theme, so no <code class="font-mono text-base">dark:</code>
                variant is needed at the call site. Prefer them over raw scale values.
            </x-ui.section-heading>

            <div class="mt-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-3" data-reveal-group>
                @foreach ($semantic as $token => $usage)
                    <div class="flex items-center gap-4 rounded-xl bg-surface p-4 ring-1 ring-hairline">
                        <div
                            class="size-12 shrink-0 rounded-lg ring-1 ring-hairline"
                            style="background-color: var(--{{ $token }})"
                        ></div>
                        <div class="min-w-0">
                            <p class="truncate font-mono text-xs text-content">{{ $token }}</p>
                            <p class="mt-0.5 text-xs text-content-subtle">{{ $usage }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- ------------------------------------------------------------------ --}}
    <x-ui.section size="compact">
        <x-ui.container>
            <x-ui.section-heading eyebrow="Colour" title="Gradients">
                The full palette, navy through magenta. Used for the logo mark, primary
                gradient buttons and hairline borders.
            </x-ui.section-heading>

            <div class="mt-10 grid gap-4 md:grid-cols-3" data-reveal-group>
                <div class="flex h-32 items-center justify-center rounded-2xl bg-brand-gradient font-display font-semibold text-white">
                    .bg-brand-gradient
                </div>
                <div class="flex h-32 items-center justify-center rounded-2xl bg-surface-raised ring-1 ring-hairline">
                    <span class="text-brand-gradient font-display text-2xl font-bold">.text-brand-gradient</span>
                </div>
                <div class="ring-gradient flex h-32 items-center justify-center rounded-2xl bg-surface-raised font-display font-semibold">
                    .ring-gradient
                </div>
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- ------------------------------------------------------------------ --}}
    <x-ui.section tone="raised" size="compact">
        <x-ui.container>
            <x-ui.section-heading eyebrow="Type" title="Typography">
                Space Grotesk for display, Inter for body, IBM Plex Sans Arabic for both roles in Arabic.
            </x-ui.section-heading>

            <div class="mt-10 grid gap-10 lg:grid-cols-2">
                <div class="flex flex-col gap-5" dir="ltr">
                    <p class="text-xs font-semibold tracking-wider text-content-subtle uppercase">Latin</p>
                    <p class="font-display text-5xl leading-tight font-bold">Display / 700</p>
                    <p class="font-display text-3xl font-medium">Display / 500</p>
                    <p class="text-xl font-semibold">Body / 600</p>
                    <p class="text-base leading-relaxed text-content-muted">
                        Body / 400 — We design and build mobile apps, web platforms, dashboards,
                        ERP and AI systems for companies that need software they can rely on.
                    </p>
                    <p class="text-sm text-content-subtle">Caption / 400 — supporting metadata and labels.</p>
                </div>

                <div class="flex flex-col gap-5 font-arabic" dir="rtl">
                    <p class="text-xs font-semibold tracking-wider text-content-subtle uppercase">Arabic</p>
                    <p class="text-5xl leading-tight font-bold">عنوان رئيسي / 700</p>
                    <p class="text-3xl font-medium">عنوان فرعي / 500</p>
                    <p class="text-xl font-semibold">نص بارز / 600</p>
                    <p class="text-base leading-loose text-content-muted">
                        نص عادي / 400 — نصمّم ونبني تطبيقات الجوال ومنصات الويب ولوحات التحكم
                        وأنظمة تخطيط الموارد والذكاء الاصطناعي للشركات التي تحتاج برمجيات يُعتمد عليها.
                    </p>
                    <p class="text-sm text-content-subtle">تسمية / 400 — بيانات مساعدة وتسميات.</p>
                </div>
            </div>

            <p class="mt-8 max-w-3xl text-sm text-content-subtle">
                Arabic renders optically smaller than Latin at the same pixel size, so RTL pages
                scale the root font by 1.06 and increase leading to 1.85. That is handled globally
                in <code class="font-mono text-xs">app.css</code> — do not compensate per component.
            </p>
        </x-ui.container>
    </x-ui.section>

    {{-- ------------------------------------------------------------------ --}}
    <x-ui.section size="compact">
        <x-ui.container>
            <x-ui.section-heading eyebrow="Components" title="Buttons">
                Magenta is reserved for the single most important action on a page.
                If two buttons are both magenta, one of them is wrong.
            </x-ui.section-heading>

            <div class="mt-10 flex flex-col gap-8">
                @foreach ($buttonVariants as $variant)
                    <div class="flex flex-col gap-3">
                        <p class="font-mono text-xs text-content-subtle">variant="{{ $variant }}"</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.button :variant="$variant" size="sm">Small</x-ui.button>
                            <x-ui.button :variant="$variant" size="md">Medium</x-ui.button>
                            <x-ui.button :variant="$variant" size="lg">Large</x-ui.button>
                            <x-ui.button :variant="$variant" size="md" icon="arrow-right">With icon</x-ui.button>
                            <x-ui.button :variant="$variant" size="md" disabled>Disabled</x-ui.button>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- ------------------------------------------------------------------ --}}
    <x-ui.section tone="raised" size="compact">
        <x-ui.container>
            <x-ui.section-heading eyebrow="Components" title="Badges & cards" />

            <div class="mt-10 flex flex-wrap gap-3">
                <x-ui.badge>Neutral</x-ui.badge>
                <x-ui.badge tone="accent">Accent</x-ui.badge>
                <x-ui.badge tone="brand">Brand</x-ui.badge>
                <x-ui.badge tone="iris">Iris</x-ui.badge>
            </div>

            <div class="mt-8 grid gap-5 md:grid-cols-3" data-reveal-group>
                @foreach (array_slice(config('site.services'), 0, 3) as $service)
                    <x-ui.card href="#">
                        <span class="grid size-11 place-items-center rounded-xl bg-brand-500/12 text-brand-300 transition-colors group-hover:bg-accent-500/15 group-hover:text-accent-400">
                            <x-ui.icon :name="$service['icon']" size="size-5" />
                        </span>
                        <h3 class="mt-5 font-display text-lg font-bold">
                            {{ __("services.{$service['key']}.title") }}
                        </h3>
                        <p class="mt-2 grow text-sm leading-relaxed text-content-muted">
                            {{ __("services.{$service['key']}.excerpt") }}
                        </p>
                        <span class="mt-5 inline-flex items-center gap-1.5 text-sm font-medium text-accent-400">
                            {{ __('common.learn_more') }}
                            <x-ui.icon name="arrow-right" size="size-4" class="transition-transform duration-200 group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1" />
                        </span>
                    </x-ui.card>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- ------------------------------------------------------------------ --}}
    <x-ui.section size="compact">
        <x-ui.container>
            <x-ui.section-heading eyebrow="Components" title="Icons">
                Inlined rather than sprited — they sit on the critical path in the nav and buttons.
            </x-ui.section-heading>

            <div class="mt-10 grid grid-cols-3 gap-3 sm:grid-cols-5 lg:grid-cols-8">
                @foreach ([...$iconNames, ...$brandIcons] as $icon)
                    <div class="flex flex-col items-center gap-2 rounded-xl bg-surface-raised p-4 ring-1 ring-hairline">
                        <x-ui.icon :name="$icon" size="size-6" class="text-content-muted" />
                        <p class="truncate text-center font-mono text-[10px] text-content-subtle">{{ $icon }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.container>
    </x-ui.section>

    {{-- ------------------------------------------------------------------ --}}
    <x-ui.section tone="raised" size="compact">
        <x-ui.container>
            <x-ui.section-heading eyebrow="Motion" title="Animation">
                Scroll-reveal and count-ups. Both respect
                <code class="font-mono text-base">prefers-reduced-motion</code> — turn it on in your OS
                and reload to confirm nothing moves.
            </x-ui.section-heading>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl bg-surface p-6 text-center ring-1 ring-hairline">
                    <p class="font-display text-4xl font-bold text-brand-gradient" data-countup="2500" data-suffix="+">0</p>
                    <p class="mt-2 text-sm text-content-muted">Projects</p>
                </div>
                <div class="rounded-2xl bg-surface p-6 text-center ring-1 ring-hairline">
                    <p class="font-display text-4xl font-bold text-brand-gradient" data-countup="73">0</p>
                    <p class="mt-2 text-sm text-content-muted">NPS</p>
                </div>
                <div class="rounded-2xl bg-surface p-6 text-center ring-1 ring-hairline">
                    <p class="font-display text-4xl font-bold text-brand-gradient" data-countup="4.9" data-decimals="1">0</p>
                    <p class="mt-2 text-sm text-content-muted">Store rating</p>
                </div>
                <div class="rounded-2xl bg-surface p-6 text-center ring-1 ring-hairline">
                    <p class="font-display text-4xl font-bold text-brand-gradient" data-countup="99.9" data-decimals="1" data-suffix="%">0</p>
                    <p class="mt-2 text-sm text-content-muted">Uptime</p>
                </div>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (['up', 'start', 'end', 'scale'] as $axis)
                    <div class="rounded-xl bg-surface p-6 ring-1 ring-hairline" data-reveal="{{ $axis }}">
                        <p class="font-mono text-xs text-content-subtle">data-reveal="{{ $axis }}"</p>
                    </div>
                @endforeach
            </div>

            <p class="mt-6 max-w-3xl text-sm text-content-subtle">
                <code class="font-mono text-xs">start</code> and <code class="font-mono text-xs">end</code>
                are direction-relative, not left/right — they mirror automatically in Arabic.
                Count-ups format their numbers with the page locale, so Arabic pages show Arabic-Indic digits.
            </p>
        </x-ui.container>
    </x-ui.section>

    {{-- ------------------------------------------------------------------ --}}
    <x-ui.section size="compact">
        <x-ui.container>
            <x-ui.section-heading eyebrow="Layout" title="Direction">
                Current document state, for verifying the RTL build.
            </x-ui.section-heading>

            <dl class="mt-10 grid gap-3 sm:grid-cols-3">
                @foreach ([
                    'Locale' => Locale::current(),
                    'Direction' => Locale::direction(),
                    'Font stack' => Locale::isRtl() ? 'IBM Plex Sans Arabic' : 'Inter / Space Grotesk',
                ] as $label => $value)
                    <div class="rounded-xl bg-surface-raised p-5 ring-1 ring-hairline">
                        <dt class="text-xs tracking-wider text-content-subtle uppercase">{{ $label }}</dt>
                        <dd class="mt-1 font-mono text-sm text-content">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            <p class="mt-6 max-w-3xl text-sm text-content-subtle">
                Spacing and positioning use logical properties throughout
                (<code class="font-mono text-xs">ms-</code>, <code class="font-mono text-xs">pe-</code>,
                <code class="font-mono text-xs">start-</code>, <code class="font-mono text-xs">end-</code>).
                Never use <code class="font-mono text-xs">ml-</code>, <code class="font-mono text-xs">mr-</code>,
                <code class="font-mono text-xs">left-</code> or <code class="font-mono text-xs">right-</code> —
                they do not mirror.
            </p>
        </x-ui.container>
    </x-ui.section>
</x-layouts.app>
