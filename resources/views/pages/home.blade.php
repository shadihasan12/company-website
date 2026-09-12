<x-layouts.app :description="__('home.hero.subhead')">
    <x-sections.hero :shipped-count="$shippedCount" />

    <x-sections.client-strip :clients="$clients" />

    <x-sections.services :services="$services" />

    <x-sections.work :projects="$featuredProjects" />

    <x-sections.stats :stats="$stats" />

    <x-sections.process />

    <x-sections.tech :technologies="$technologies" />

    <x-sections.testimonials :testimonials="$testimonials" />
</x-layouts.app>
