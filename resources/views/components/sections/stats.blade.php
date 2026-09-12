@props(['stats'])

@if ($stats->count() >= 2)
    <section class="relative isolate overflow-hidden border-y border-hairline bg-surface-sunken py-16 sm:py-20">
        <div class="glow -top-32 start-1/3 size-[30rem] bg-brand-500/20" aria-hidden="true"></div>

        <x-ui.container class="relative">
            <dl class="grid grid-cols-2 gap-y-10 text-center lg:grid-cols-4" data-reveal-group>
                @foreach ($stats as $stat)
                    <div class="flex flex-col items-center gap-1">
                        {{-- The rendered value is the real number, so the
                             figure is correct with JavaScript disabled; the
                             count-up only animates from zero up to it. --}}
                        <dd
                            class="font-display text-4xl font-bold text-brand-gradient sm:text-5xl"
                            data-countup="{{ $stat['value'] }}"
                            data-decimals="{{ $stat['decimals'] }}"
                            @if ($stat['suffix']) data-suffix="{{ $stat['suffix'] }}" @endif
                        >{{ number_format((float) $stat['value'], $stat['decimals']) }}{{ $stat['suffix'] }}</dd>

                        <dt class="text-sm text-content-muted">
                            {{ __("home.stats.{$stat['key']}") }}
                        </dt>
                    </div>
                @endforeach
            </dl>
        </x-ui.container>
    </section>
@endif
