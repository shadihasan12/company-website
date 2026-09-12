<button
    type="button"
    x-data
    x-on:click="$store.theme.toggle()"
    :aria-pressed="$store.theme.isDark ? 'true' : 'false'"
    aria-label="{{ __('nav.toggle_theme') }}"
    title="{{ __('nav.toggle_theme') }}"
    {{ $attributes->class([
        'grid size-10 place-items-center rounded-full text-content-muted',
        'transition-colors hover:bg-surface-raised hover:text-content',
    ]) }}
>
    {{-- Both icons are rendered and toggled with `hidden`, so the control
         never reflows when the theme changes. --}}
    <x-ui.icon name="sun" class="hidden dark:block" />
    <x-ui.icon name="moon" class="block dark:hidden" />
</button>
