@php
    // Error pages render outside the session middleware, so $errors is not
    // shared. Fall back to an empty bag rather than taking the page down.
    $errors ??= new \Illuminate\Support\ViewErrorBag;
@endphp

<form method="POST" action="{{ route('newsletter.store') }}" {{ $attributes->class(['relative flex flex-col gap-2']) }}>
    @csrf
    <x-ui.honeypot />

    <label for="newsletter-email" class="text-xs font-semibold tracking-wider text-content uppercase">
        {{ __('contact.newsletter.title') }}
    </label>

    <div class="flex gap-2">
        <input
            id="newsletter-email"
            type="email"
            name="email"
            required
            placeholder="{{ __('contact.newsletter.placeholder') }}"
            autocomplete="email"
            class="min-w-0 grow rounded-full bg-surface px-4 py-2.5 text-sm text-content ring-1 ring-hairline transition-colors placeholder:text-content-subtle focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-400"
        >

        <x-ui.button type="submit" size="sm" class="shrink-0">
            {{ __('contact.newsletter.submit') }}
        </x-ui.button>
    </div>

    @if (session('newsletter'))
        <p role="status" class="text-xs text-accent-400">{{ session('newsletter') }}</p>
    @endif

    @error('email')
        <p class="text-xs text-red-400">{{ $message }}</p>
    @enderror
</form>
