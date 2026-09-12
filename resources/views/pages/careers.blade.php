<x-layouts.app :title="__('careers.title')" :description="__('careers.subhead')">
    <x-ui.section size="compact">
        <div class="glow -top-32 start-1/3 size-[30rem] bg-brand-500/25" aria-hidden="true"></div>

        <x-ui.container size="narrow" class="relative">
            <x-ui.section-heading :title="__('careers.heading')" as="h1" align="center">
                {{ __('careers.subhead') }}
            </x-ui.section-heading>

            @if (session('application'))
                <p role="status" class="mt-8 rounded-xl bg-accent-500/10 p-4 text-center text-sm text-accent-300 ring-1 ring-accent-500/25">
                    {{ session('application') }}
                </p>
            @endif

            <form method="POST" action="{{ route('careers.store') }}" class="relative mt-10 flex flex-col gap-5">
                @csrf
                <x-ui.honeypot />

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-ui.field name="name" :label="__('contact.fields.name')" required />
                    <x-ui.field name="email" type="email" :label="__('contact.fields.email')" required />
                </div>

                <x-ui.field name="role" :label="__('careers.fields.role')" />
                <x-ui.field name="portfolio" type="url" :label="__('careers.fields.portfolio')" placeholder="https://" />
                <x-ui.field name="message" :label="__('careers.fields.message')" :rows="6" :placeholder="__('careers.placeholder')" required />

                <div>
                    <x-ui.button type="submit" size="lg" icon="arrow-right">{{ __('careers.submit') }}</x-ui.button>
                </div>
            </form>
        </x-ui.container>
    </x-ui.section>
</x-layouts.app>
