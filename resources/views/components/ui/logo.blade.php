@props([
    'size' => 'md',
    'markOnly' => false,
])

@php
    /*
     | The mark below is a placeholder approximation of the Clean Cody
     | symbol — the gradient "C" ring with its orbit sweep — drawn so the
     | site is visually correct before the real artwork arrives.
     |
     | To swap in the real logo, drop either file into public/images/ and
     | this component picks it up automatically, no code change needed:
     |
     |   public/images/logo-mark.svg   (preferred)
     |   public/images/logo-mark.png
     */
    $markFile = collect(['logo-mark.svg', 'logo-mark.png'])
        ->first(fn (string $file) => file_exists(public_path("images/{$file}")));

    $mark = match ($size) {
        'sm' => 'size-8',
        'lg' => 'size-14',
        default => 'size-10',
    };

    $text = match ($size) {
        'sm' => 'text-base',
        'lg' => 'text-2xl',
        default => 'text-xl',
    };

    // Gradient ids must be unique — two logos on one page would otherwise
    // share (and fight over) the same definitions.
    $uid = 'logo-'.Str::random(6);
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-2.5']) }}>
    @if ($markFile)
        <img
            src="{{ asset("images/{$markFile}") }}"
            alt="{{ config('site.name') }}"
            class="{{ $mark }} shrink-0 object-contain"
            width="40"
            height="40"
        >
    @else
        <svg class="{{ $mark }} shrink-0" viewBox="0 0 100 100" fill="none" aria-hidden="true">
            <defs>
                <linearGradient id="{{ $uid }}-ring" x1="18" y1="86" x2="84" y2="16" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="var(--color-brand-navy)" />
                    <stop offset="45%" stop-color="var(--color-brand-violet)" />
                    <stop offset="100%" stop-color="var(--color-brand-magenta)" />
                </linearGradient>
                <linearGradient id="{{ $uid }}-orbit" x1="10" y1="30" x2="92" y2="72" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="var(--color-brand-iris)" />
                    <stop offset="60%" stop-color="var(--color-brand-violet)" />
                    <stop offset="100%" stop-color="var(--color-brand-navy)" />
                </linearGradient>
            </defs>

            {{-- Orbit sweep, behind the ring. --}}
            <ellipse
                cx="50" cy="50" rx="46" ry="21"
                transform="rotate(-32 50 50)"
                stroke="url(#{{ $uid }}-orbit)"
                stroke-width="5"
                opacity="0.85"
            />

            {{-- The "C": a thick arc left open on the right. --}}
            <path
                d="M 76 28.2 A 34 34 0 1 0 76 71.8"
                stroke="url(#{{ $uid }}-ring)"
                stroke-width="21"
            />

            {{-- Inward tab that closes the counter of the C. --}}
            <rect
                x="58" y="43.5" width="20" height="13" rx="2"
                fill="url(#{{ $uid }}-ring)"
            />
        </svg>
    @endif

    @unless ($markOnly)
        {{-- Wordmark. Uppercase, tight tracking, matching the lockup. --}}
        <span class="{{ $text }} font-display leading-none font-bold tracking-tight uppercase">
            {{ config('site.name') }}
        </span>
    @endunless
</span>
