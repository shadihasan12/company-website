@if (\App\Rules\Turnstile::isConfigured())
    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="auto"></div>

    @push('scripts')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endpush

    @error('cf-turnstile-response')
        <p class="text-sm text-red-400">{{ $message }}</p>
    @enderror
@endif
