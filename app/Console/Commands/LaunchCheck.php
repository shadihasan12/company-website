<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Post;
use App\Models\Project;
use App\Models\Testimonial;
use App\Models\User;
use App\Rules\Turnstile;
use App\Support\Seo;
use Illuminate\Console\Command;

/**
 * Pre-launch readiness check.
 *
 * Separate from `content:audit`, which is about editorial gaps. This is
 * about configuration that is easy to forget and expensive to get wrong —
 * debug left on, a staging URL in production, no admin account.
 */
class LaunchCheck extends Command
{
    protected $signature = 'launch:check {--strict : Exit non-zero if anything is blocking}';

    protected $description = 'Check configuration and content readiness before going live';

    protected int $blocking = 0;

    protected int $warnings = 0;

    public function handle(): int
    {
        $this->newLine();
        $this->components->info('Configuration');
        $this->checkConfiguration();

        $this->newLine();
        $this->components->info('Brand assets');
        $this->checkAssets();

        $this->newLine();
        $this->components->info('Content');
        $this->checkContent();

        $this->newLine();

        if ($this->blocking > 0) {
            $this->components->error("{$this->blocking} blocking issue(s), {$this->warnings} warning(s).");

            return $this->option('strict') ? self::FAILURE : self::SUCCESS;
        }

        $this->warnings > 0
            ? $this->components->warn("Ready to launch, with {$this->warnings} warning(s).")
            : $this->components->info('Ready to launch.');

        return self::SUCCESS;
    }

    protected function checkConfiguration(): void
    {
        $this->assert('APP_KEY is set', filled(config('app.key')));
        $this->assert('APP_DEBUG is off', ! config('app.debug'));
        $this->assert('APP_ENV is production', app()->isProduction());
        $this->assert(
            'APP_URL is not localhost',
            ! str_contains(config('app.url'), 'localhost') && ! str_contains(config('app.url'), '127.0.0.1'),
        );
        $this->assert('APP_URL uses HTTPS', str_starts_with(config('app.url'), 'https://'));
        $this->assert('Site is indexable', Seo::isIndexable());
        $this->assert(
            'Mail is not the log driver',
            ! in_array(config('mail.default'), ['log', 'array'], true),
        );
        $this->assert('Lead notification address is set', filled(config('site.leads.notify')));
        $this->assert('An admin account exists', User::where('is_admin', true)->exists());
        $this->assert('Storage is linked', is_link(public_path('storage')) || is_dir(public_path('storage')));
        $this->assert(
            'Database is not SQLite',
            config('database.default') !== 'sqlite',
            blocking: false,
        );

        $this->assert('Turnstile is configured', Turnstile::isConfigured(), blocking: false);
        $this->assert(
            'Analytics is configured',
            filled(config('services.ga4.id')) || filled(config('services.plausible.domain')),
            blocking: false,
        );
    }

    protected function checkAssets(): void
    {
        $this->assert(
            'Logo file present (public/images/logo-mark.svg)',
            file_exists(public_path('images/logo-mark.svg')) || file_exists(public_path('images/logo-mark.png')),
            blocking: false,
        );

        $this->assert(
            'Social sharing image present (public/images/og-default.png)',
            file_exists(public_path(config('site.og_image'))),
            blocking: false,
        );

        $this->assert('Built assets present', file_exists(public_path('build/manifest.json')));
    }

    protected function checkContent(): void
    {
        $demo = Testimonial::where('author_name', 'like', 'SAMPLE%')->count()
            + Post::where('slug', 'like', 'sample-post-%')->count();

        // Sample content reaching production is the failure this whole
        // command exists to prevent.
        $this->assert("No SAMPLE demo content remains ({$demo} found)", $demo === 0);

        $this->assert('At least one published case study', Project::published()->exists());

        $evidenced = Project::published()->get()->filter->is_evidenced->count();
        $this->assert(
            "Case studies with screenshots, results and a link ({$evidenced})",
            $evidenced > 0,
            blocking: false,
        );

        $this->assert(
            'At least one client logo uploaded',
            Client::named()->whereNotNull('logo_path')->exists(),
            blocking: false,
        );

        $this->assert('At least one testimonial', Testimonial::exists(), blocking: false);
    }

    protected function assert(string $label, bool $passed, bool $blocking = true): void
    {
        if (! $passed) {
            $blocking ? $this->blocking++ : $this->warnings++;
        }

        $this->components->twoColumnDetail(
            $label,
            match (true) {
                $passed => '<fg=green>ok</>',
                $blocking => '<fg=red>BLOCKING</>',
                default => '<fg=yellow>warning</>',
            },
        );
    }
}
