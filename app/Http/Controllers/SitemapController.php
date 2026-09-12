<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Support\Locale;
use App\Support\Seo;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect();

        foreach (array_keys(Locale::all()) as $locale) {
            $urls = $urls->merge($this->urlsFor($locale));
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $lines = ['User-agent: *'];

        if (Seo::isIndexable()) {
            $lines[] = 'Disallow: /admin';
            $lines[] = 'Disallow: /'.config('site.fallback_locale').'/styleguide';
            $lines[] = '';
            $lines[] = 'Sitemap: '.url('/sitemap.xml');
        } else {
            // Staging and local copies are closed off entirely rather than
            // relying on per-page meta tags alone.
            $lines[] = 'Disallow: /';
        }

        return response(implode("\n", $lines)."\n")
            ->header('Content-Type', 'text/plain');
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: ?string, priority: string}>
     */
    protected function urlsFor(string $locale): Collection
    {
        $url = fn (string $name, mixed $parameters = []) => route($name, is_array($parameters)
            ? ['locale' => $locale, ...$parameters]
            : ['locale' => $locale, $parameters]);

        $urls = collect([
            ['loc' => $url('home'), 'lastmod' => null, 'priority' => '1.0'],
            ['loc' => $url('services.index'), 'lastmod' => null, 'priority' => '0.9'],
            ['loc' => $url('work.index'), 'lastmod' => null, 'priority' => '0.9'],
            ['loc' => $url('about'), 'lastmod' => null, 'priority' => '0.7'],
            ['loc' => $url('contact'), 'lastmod' => null, 'priority' => '0.8'],
            ['loc' => $url('scope'), 'lastmod' => null, 'priority' => '0.8'],
        ]);

        // Careers only exists while hiring is switched on.
        if (Route::has('careers')) {
            $urls->push(['loc' => $url('careers'), 'lastmod' => null, 'priority' => '0.5']);
        }

        foreach (Service::published()->ordered()->get() as $service) {
            $urls->push([
                'loc' => $url('services.show', $service->slug),
                'lastmod' => $service->updated_at?->toAtomString(),
                'priority' => '0.8',
            ]);
        }

        foreach (Project::published()->ordered()->get() as $project) {
            $urls->push([
                'loc' => $url('work.show', $project->slug),
                'lastmod' => $project->updated_at?->toAtomString(),
                'priority' => '0.8',
            ]);
        }

        $posts = Post::published()->latestFirst()->get();

        if ($posts->isNotEmpty()) {
            $urls->push(['loc' => $url('posts.index'), 'lastmod' => null, 'priority' => '0.7']);
        }

        foreach ($posts as $post) {
            $urls->push([
                'loc' => $url('posts.show', $post->slug),
                'lastmod' => $post->updated_at?->toAtomString(),
                'priority' => '0.6',
            ]);
        }

        return $urls;
    }
}
