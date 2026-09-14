<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

/**
 * Captures a screenshot of a project's live website.
 *
 * Website projects have no store listing to pull images from, so the page
 * is rendered in headless Chrome. Run by hand, never at request time.
 */
class CaptureSiteScreenshots extends Command
{
    protected $signature = 'portfolio:capture-sites
        {--project= : Limit to one project slug}
        {--force : Replace an existing gallery}
        {--width=1440}
        {--height=1800}';

    protected $description = 'Screenshot the live website of each web project';

    /**
     * Common install locations. Chrome is a developer tool here, not a
     * production dependency — the command simply reports if it is absent.
     *
     * @var list<string>
     */
    protected const CHROME_PATHS = [
        '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        '/Applications/Chromium.app/Contents/MacOS/Chromium',
        '/usr/bin/google-chrome',
        '/usr/bin/chromium',
        '/usr/bin/chromium-browser',
    ];

    public function handle(): int
    {
        $chrome = collect(self::CHROME_PATHS)->first(fn (string $path) => is_executable($path));

        if (! $chrome) {
            $this->components->error('No Chrome or Chromium binary found.');

            return self::FAILURE;
        }

        $projects = Project::query()
            ->when($this->option('project'), fn ($query, $slug) => $query->where('slug', $slug))
            ->whereNotNull('website_url')
            ->whereNull('app_store_url')
            ->whereNull('google_play_url')
            ->get();

        if ($projects->isEmpty()) {
            $this->components->warn('No website-only projects with a URL.');

            return self::SUCCESS;
        }

        foreach ($projects as $project) {
            $this->capture($chrome, $project);
        }

        $this->newLine();
        $this->components->info('Done.');

        return self::SUCCESS;
    }

    protected function capture(string $chrome, Project $project): void
    {
        $this->newLine();
        $this->components->info($project->name);

        if (filled($project->gallery) && ! $this->option('force')) {
            $this->components->warn('  Already has a gallery — pass --force to replace it.');

            return;
        }

        $temporary = tempnam(sys_get_temp_dir(), 'shot').'.png';

        $process = new Process([
            $chrome,
            '--headless',
            '--disable-gpu',
            '--hide-scrollbars',
            '--no-sandbox',
            // Lets fonts, images and any entry animation settle before the
            // frame is taken, otherwise the capture is a half-built page.
            '--virtual-time-budget=9000',
            "--window-size={$this->option('width')},{$this->option('height')}",
            "--screenshot={$temporary}",
            $project->website_url,
        ]);

        $process->setTimeout(90)->run();

        if (! file_exists($temporary) || filesize($temporary) === 0) {
            $this->components->warn("  Capture failed for {$project->website_url}");

            return;
        }

        $binary = (string) file_get_contents($temporary);
        @unlink($temporary);

        $optimised = $this->optimise($binary);
        $path = "projects/gallery/{$project->slug}-01.webp";

        Storage::disk('public')->put($path, $optimised ?? $binary);

        $project->update([
            'gallery' => [$path],
            'hero_image_path' => $path,
        ]);

        $this->components->twoColumnDetail(
            "  {$path}",
            $this->humanise(strlen($binary)).' -> '.$this->humanise(strlen($optimised ?? $binary)),
        );
    }

    /**
     * Full-page captures are multi-megabyte PNGs. Re-encoded to WebP at a
     * sensible width they drop by an order of magnitude.
     */
    protected function optimise(string $binary): ?string
    {
        $source = @imagecreatefromstring($binary);

        if ($source === false) {
            return null;
        }

        if (imagesx($source) > 1440) {
            $scaled = imagescale($source, 1440);

            if ($scaled !== false) {
                imagedestroy($source);
                $source = $scaled;
            }
        }

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
