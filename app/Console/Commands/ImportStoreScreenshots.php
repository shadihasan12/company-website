<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Downloads phone screenshots from a project's store listings.
 *
 * Run once, by hand — never at request time. The images are stored on our
 * own disk rather than hotlinked from Apple's or Google's CDN.
 *
 * Apple is read through the public iTunes lookup API, which returns
 * screenshot URLs directly. Google has no equivalent, so the listing page
 * is parsed; that is inherently brittle, which is another reason this is a
 * one-off import rather than anything the site depends on.
 */
class ImportStoreScreenshots extends Command
{
    protected $signature = 'portfolio:import-screenshots
        {--project= : Limit to one project slug}
        {--limit=8 : Maximum screenshots to keep per project}
        {--force : Replace an existing gallery}
        {--dry-run : Report what would be downloaded without writing anything}';

    protected $description = 'Import phone screenshots from App Store and Google Play listings';

    protected const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36';

    public function handle(): int
    {
        $projects = Project::query()
            ->when($this->option('project'), fn ($query, $slug) => $query->where('slug', $slug))
            ->where(fn ($query) => $query->whereNotNull('app_store_url')->orWhereNotNull('google_play_url'))
            ->get();

        if ($projects->isEmpty()) {
            $this->components->warn('No projects with store URLs.');

            return self::SUCCESS;
        }

        foreach ($projects as $project) {
            $this->importFor($project);
        }

        $this->newLine();
        $this->components->info($this->option('dry-run') ? 'Dry run complete.' : 'Done.');

        return self::SUCCESS;
    }

    protected function importFor(Project $project): void
    {
        $this->newLine();
        $this->components->info($project->name);

        if (filled($project->gallery) && ! $this->option('force')) {
            $this->components->warn('  Already has a gallery — pass --force to replace it.');

            return;
        }

        $urls = collect([
            ...$this->appleScreenshots($project->app_store_url),
            ...$this->googleScreenshots($project->google_play_url),
        ])->unique()->values();

        if ($urls->isEmpty()) {
            $this->components->warn('  No screenshots found.');

            return;
        }

        $this->line("  Found {$urls->count()} candidate image(s).");

        $stored = [];

        foreach ($urls as $url) {
            if (count($stored) >= (int) $this->option('limit')) {
                break;
            }

            if ($path = $this->store($project, $url, count($stored) + 1)) {
                $stored[] = $path;
            }
        }

        if ($stored === []) {
            $this->components->warn('  Nothing usable after filtering.');

            return;
        }

        $this->components->twoColumnDetail('  Kept', (string) count($stored).' screenshot(s)');

        if (! $this->option('dry-run')) {
            // On --force the old gallery files are gone, so a hero that
            // pointed into them has to be repointed too. Keeping it only
            // when the file still exists left every project with a broken
            // hero after the first re-import.
            $heroStillValid = filled($project->hero_image_path)
                && Storage::disk('public')->exists($project->hero_image_path);

            $project->update([
                'gallery' => $stored,
                'hero_image_path' => $heroStillValid ? $project->hero_image_path : $stored[0],
            ]);
        }
    }

    /**
     * Apple's public lookup API returns screenshot URLs directly, so no
     * page parsing is needed. `screenshotUrls` is the iPhone set; iPad and
     * Apple TV live in separate keys and are deliberately ignored.
     *
     * @return list<string>
     */
    protected function appleScreenshots(?string $storeUrl): array
    {
        if (blank($storeUrl) || ! preg_match('/id(\d+)/', $storeUrl, $matches)) {
            return [];
        }

        try {
            $response = Http::timeout(20)->get('https://itunes.apple.com/lookup', ['id' => $matches[1]]);
        } catch (\Throwable $exception) {
            $this->components->warn("  Apple lookup failed: {$exception->getMessage()}");

            return [];
        }

        $result = $response->json('results.0');

        if (! $result) {
            $this->components->warn('  Apple lookup returned nothing for id '.$matches[1]);

            return [];
        }

        // The API hands back 320px-wide thumbnails. The trailing segment is
        // a size instruction to Apple's image CDN, so rewriting it to
        // 2000x0w returns the screenshot at its native resolution.
        return collect($result['screenshotUrls'] ?? [])
            ->map(fn (string $url) => preg_replace('#/\d+x\d+[a-z]{0,3}\.(png|jpg|jpeg|webp)$#i', '/2000x0w.png', $url))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Google has no public API, so the listing HTML is parsed. Every image
     * variant is normalised to its base id and requested at a large width;
     * what is actually a screenshot is decided later, from the downloaded
     * file's dimensions.
     *
     * @return list<string>
     */
    protected function googleScreenshots(?string $storeUrl): array
    {
        if (blank($storeUrl)) {
            return [];
        }

        try {
            $html = Http::withHeaders(['User-Agent' => self::USER_AGENT])
                ->timeout(25)
                ->get($storeUrl)
                ->body();
        } catch (\Throwable $exception) {
            $this->components->warn("  Google Play fetch failed: {$exception->getMessage()}");

            return [];
        }

        preg_match_all('#https://play-lh\.googleusercontent\.com/([A-Za-z0-9_\-]+)=#', $html, $matches);

        return collect($matches[1])
            ->unique()
            ->map(fn (string $id) => "https://play-lh.googleusercontent.com/{$id}=w1600")
            ->values()
            ->all();
    }

    /**
     * Downloads one image and keeps it only if it looks like a phone
     * screenshot. App icons are square and feature graphics are wide, so
     * filtering on aspect ratio removes both without needing to know which
     * URL was which.
     */
    protected function store(Project $project, string $url, int $index): ?string
    {
        try {
            $response = Http::withHeaders(['User-Agent' => self::USER_AGENT])->timeout(30)->get($url);
        } catch (\Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $binary = $response->body();
        $size = @getimagesizefromstring($binary);

        if (! $size) {
            return null;
        }

        [$width, $height] = $size;

        // Portrait, and big enough to be worth showing.
        if ($height <= $width * 1.2 || $width < 400) {
            return null;
        }

        $path = "projects/gallery/{$project->slug}-".Str::padLeft((string) $index, 2, '0').'.webp';

        // Store screenshots arrive at native device resolution — 1290x2796
        // PNGs, several megabytes each. Shipping those to a browser would
        // ruin the page they are meant to sell. Downscaled and re-encoded
        // to WebP, they land around a fiftieth of the size with no visible
        // difference at the sizes the site actually renders them.
        $optimised = $this->optimise($binary, $width, $height);

        $this->line(sprintf(
            '    %dx%d  %s  %s -> %s',
            $width,
            $height,
            $path,
            $this->humanise(strlen($binary)),
            $this->humanise(strlen($optimised ?? $binary)),
        ));

        if (! $this->option('dry-run')) {
            Storage::disk('public')->put($path, $optimised ?? $binary);
        }

        return $path;
    }

    /**
     * Downscale to a sensible web width and re-encode as WebP.
     *
     * Returns null if GD cannot handle the image, in which case the caller
     * falls back to storing the original rather than losing the screenshot.
     */
    protected function optimise(string $binary, int $width, int $height): ?string
    {
        $maxWidth = 1080;

        $source = @imagecreatefromstring($binary);

        if ($source === false) {
            return null;
        }

        if ($width > $maxWidth) {
            $target = imagescale($source, $maxWidth);

            if ($target !== false) {
                imagedestroy($source);
                $source = $target;
            }
        }

        // Screenshots can carry transparency around rounded corners.
        imagepalettetotruecolor($source);
        imagealphablending($source, true);
        imagesavealpha($source, true);

        ob_start();
        $encoded = imagewebp($source, null, 82);
        $output = (string) ob_get_clean();

        imagedestroy($source);

        return $encoded ? $output : null;
    }

    protected function humanise(int $bytes): string
    {
        return $bytes > 1048576
            ? round($bytes / 1048576, 1).'MB'
            : round($bytes / 1024).'KB';
    }
}
