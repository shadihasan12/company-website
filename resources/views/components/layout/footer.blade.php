@php
    use App\Support\Nav;

    $services = config('site.services');
    $contact = config('site.contact');
    $address = config('site.address');

    // Unconfigured profiles are dropped rather than linked to a dead page.
    $social = array_filter(config('site.social'));

    $companyLinks = [
        ['label' => __('nav.about'), 'route' => 'about'],
        ['label' => __('nav.work'), 'route' => 'work.index'],
        ['label' => __('nav.insights'), 'route' => 'posts.index'],
        ['label' => __('nav.contact'), 'route' => 'contact'],
    ];
@endphp

<footer class="relative overflow-hidden border-t border-hairline bg-surface-sunken">
    {{-- Decorative glow. Sits behind content and is inert to pointers. --}}
    <div class="glow -top-40 start-1/4 size-96 bg-brand-500/25" aria-hidden="true"></div>

    <x-ui.container class="relative py-16 lg:py-20">
        <div class="grid gap-12 lg:grid-cols-12 lg:gap-8">
            <div class="flex flex-col gap-5 lg:col-span-4">
                <a href="{{ route('home') }}" aria-label="{{ config('site.name') }}">
                    <x-ui.logo />
                </a>

                <p class="max-w-sm text-sm leading-relaxed text-content-muted">
                    {{ __('footer.blurb') }}
                </p>

                @if ($social)
                    <ul class="flex items-center gap-2">
                        @foreach ($social as $network => $url)
                            <li>
                                <a
                                    href="{{ $url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="{{ ucfirst($network) }}"
                                    class="grid size-10 place-items-center rounded-full text-content-subtle ring-1 ring-hairline transition-colors hover:bg-surface-raised hover:text-accent-400"
                                >
                                    <x-ui.icon :name="$network" size="size-4" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="lg:col-span-3">
                <h2 class="text-xs font-semibold tracking-wider text-content uppercase">
                    {{ __('footer.services') }}
                </h2>
                <ul class="mt-4 flex flex-col gap-2.5">
                    @foreach ($services as $service)
                        <li>
                            <a href="{{ Nav::link('services.show') }}" class="text-sm text-content-muted transition-colors hover:text-accent-400">
                                {{ __("services.{$service['key']}.title") }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h2 class="text-xs font-semibold tracking-wider text-content uppercase">
                    {{ __('footer.company') }}
                </h2>
                <ul class="mt-4 flex flex-col gap-2.5">
                    @foreach ($companyLinks as $item)
                        <li>
                            <a href="{{ Nav::link($item['route']) }}" class="text-sm text-content-muted transition-colors hover:text-accent-400">
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h2 class="text-xs font-semibold tracking-wider text-content uppercase">
                    {{ __('footer.connect') }}
                </h2>
                <ul class="mt-4 flex flex-col gap-3">
                    <li>
                        <a href="mailto:{{ $contact['email'] }}" class="group inline-flex items-center gap-2.5 text-sm text-content-muted transition-colors hover:text-accent-400">
                            <x-ui.icon name="mail" size="size-4" class="text-content-subtle transition-colors group-hover:text-accent-400" />
                            {{ $contact['email'] }}
                        </a>
                    </li>
                    <li>
                        {{-- Tel/WhatsApp links must stay LTR: the browser would
                             otherwise reorder the leading "+" in Arabic. --}}
                        <a href="tel:{{ preg_replace('/\s+/', '', $contact['phone']) }}" dir="ltr" class="group inline-flex items-center gap-2.5 text-sm text-content-muted transition-colors hover:text-accent-400">
                            <x-ui.icon name="phone" size="size-4" class="text-content-subtle transition-colors group-hover:text-accent-400" />
                            {{ $contact['phone'] }}
                        </a>
                    </li>
                    @if ($contact['whatsapp'])
                        <li>
                            <a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" rel="noopener noreferrer" class="group inline-flex items-center gap-2.5 text-sm text-content-muted transition-colors hover:text-accent-400">
                                <x-ui.icon name="whatsapp" size="size-4" class="text-content-subtle transition-colors group-hover:text-accent-400" />
                                {{ __('common.cta_whatsapp') }}
                            </a>
                        </li>
                    @endif
                    <li class="inline-flex items-center gap-2.5 text-sm text-content-muted">
                        <x-ui.icon name="map-pin" size="size-4" class="text-content-subtle" />
                        {{ __('footer.based_in', ['city' => $address['city'], 'country' => $address['country']]) }}
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-14 flex flex-col gap-4 border-t border-hairline pt-8 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-content-subtle">
                &copy; {{ now()->year }} {{ config('site.legal_name') }}. {{ __('footer.rights') }}
            </p>

            <div class="flex items-center gap-5">
                <a href="{{ Nav::link('privacy') }}" class="text-xs text-content-subtle transition-colors hover:text-content">
                    {{ __('footer.privacy') }}
                </a>
                <a href="{{ Nav::link('terms') }}" class="text-xs text-content-subtle transition-colors hover:text-content">
                    {{ __('footer.terms') }}
                </a>
            </div>
        </div>
    </x-ui.container>
</footer>
