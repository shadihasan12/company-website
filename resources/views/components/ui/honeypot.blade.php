{{--
    Bot trap plus a fill-time token.

    The input is hidden from people and from assistive technology, so a
    genuine submission always leaves it empty. `loaded_at` is encrypted
    server-side and checked against a minimum fill time, which catches the
    bots that post the instant a page parses.

    Both run regardless of whether Turnstile keys are configured.
--}}
<div class="absolute -left-[9999px]" aria-hidden="true">
    <label for="website-field">Website</label>
    <input
        id="website-field"
        type="text"
        name="website"
        value=""
        tabindex="-1"
        autocomplete="off"
    >
</div>

<input type="hidden" name="loaded_at" value="{{ encrypt(now()->timestamp) }}">
