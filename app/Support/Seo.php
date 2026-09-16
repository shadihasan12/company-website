<?php

namespace App\Support;

use App\Models\Testimonial;

class Seo
{
    /**
     * Whether search engines may index this installation.
     *
     * Defaults to the production environment so staging and local copies
     * cannot leak into search results, and so production does not depend
     * on anyone remembering to flip a switch at launch.
     */
    public static function isIndexable(): bool
    {
        $configured = config('site.indexable');

        if ($configured !== null) {
            return filter_var($configured, FILTER_VALIDATE_BOOL);
        }

        return app()->isProduction();
    }

    /**
     * The canonical URL: the current path without query parameters.
     *
     * Filtered and paginated views share their canonical with the clean
     * listing, so duplicate variants do not compete with each other.
     */
    public static function canonical(): string
    {
        return url()->current();
    }

    /**
     * Absolute URL for a sharing image, falling back to the site default.
     *
     * Returns null when neither exists, so no broken og:image is emitted.
     */
    public static function image(?string $path = null): ?string
    {
        if (filled($path)) {
            return url(Media::url($path));
        }

        $default = config('site.og_image');

        return $default && file_exists(public_path($default))
            ? url($default)
            : null;
    }

    /**
     * hreflang alternates for the current route.
     *
     * Emits nothing while only one locale is live — a self-referencing
     * hreflang on a monolingual site is noise.
     *
     * @return array<int, array{hreflang: string, href: string}>
     */
    public static function alternates(): array
    {
        $locales = Locale::all();

        if (count($locales) < 2) {
            return [];
        }

        return collect($locales)
            ->map(fn (array $meta, string $code) => [
                'hreflang' => $meta['hreflang'],
                'href' => Locale::urlFor($code),
            ])
            ->values()
            ->push([
                'hreflang' => 'x-default',
                'href' => Locale::urlFor(config('site.fallback_locale')),
            ])
            ->all();
    }

    /**
     * The Organization node, reused by every page.
     *
     * This is how both search engines and AI assistants establish what the
     * company is, so unset values are omitted rather than left empty.
     *
     * @return array<string, mixed>
     */
    public static function organization(): array
    {
        $address = config('site.address');

        return array_filter([
            '@type' => 'Organization',
            '@id' => url('/').'#organization',
            'name' => config('site.legal_name'),
            'alternateName' => config('site.name'),
            'url' => url('/'),
            'logo' => static::image(),
            'email' => config('site.contact.email'),
            'telephone' => config('site.contact.phone'),
            'foundingDate' => (string) config('site.founded_year'),
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $address['street'] ?: null,
                'addressLocality' => $address['city'] ?: null,
                'addressCountry' => $address['country_code'] ?: null,
            ]),
            'sameAs' => array_values(array_filter(config('site.social'))) ?: null,
            'aggregateRating' => static::aggregateRating(),
        ]);
    }

    /**
     * Aggregate rating from real testimonials, or null.
     *
     * Publishing an AggregateRating without genuine reviews behind it is
     * fabricated social proof and a manual-action risk with search engines,
     * so this returns null until rated testimonials actually exist.
     *
     * @return array<string, mixed>|null
     */
    public static function aggregateRating(): ?array
    {
        // Memoised: the Organization node is emitted on every page, and
        // this otherwise costs a count and an average each time.
        return PerRequest::remember('seo.aggregate-rating', fn () => static::computeAggregateRating());
    }

    /**
     * @return array<string, mixed>|null
     */
    protected static function computeAggregateRating(): ?array
    {
        $rated = Testimonial::whereNotNull('rating');
        $count = $rated->count();

        if ($count === 0) {
            return null;
        }

        return [
            '@type' => 'AggregateRating',
            'ratingValue' => round((float) $rated->avg('rating'), 1),
            'reviewCount' => $count,
            'bestRating' => 5,
            'worstRating' => 1,
        ];
    }

    /**
     * A BreadcrumbList from label => url pairs.
     *
     * @param  array<string, string>  $trail
     * @return array<string, mixed>
     */
    public static function breadcrumbs(array $trail): array
    {
        $position = 0;

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($trail)
                ->map(fn (string $url, string $name) => [
                    '@type' => 'ListItem',
                    'position' => ++$position,
                    'name' => $name,
                    'item' => $url,
                ])
                ->values()
                ->all(),
        ];
    }
}
