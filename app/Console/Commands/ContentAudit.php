<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Console\Command;

/**
 * Reports what is still missing before the site can credibly launch.
 *
 * The research on B2B sites is consistent: screenshots, named clients and
 * measurable outcomes are what convert. This command makes the absence of
 * them visible instead of letting placeholder pages quietly ship.
 */
class ContentAudit extends Command
{
    protected $signature = 'content:audit {--strict : Exit non-zero when anything is missing}';

    protected $description = 'List the content gaps that block launch';

    public function handle(): int
    {
        $gaps = 0;

        $gaps += $this->auditProjects();
        $gaps += $this->auditClients();
        $gaps += $this->auditServices();
        $gaps += $this->auditSocialProof();

        $this->newLine();

        if ($gaps === 0) {
            $this->components->info('No content gaps. Ready to launch.');

            return self::SUCCESS;
        }

        $this->components->warn("{$gaps} content gap(s) outstanding.");

        return $this->option('strict') ? self::FAILURE : self::SUCCESS;
    }

    protected function auditProjects(): int
    {
        $this->components->twoColumnDetail('<fg=cyan;options=bold>PROJECTS</>', '');

        $rows = [];
        $gaps = 0;

        foreach (Project::published()->ordered()->get() as $project) {
            $missing = collect([
                'screenshots' => blank($project->gallery),
                'metrics' => blank($project->metrics),
                'public link' => ! $project->has_public_link,
                'problem' => $project->problem->isEmpty(),
                'outcome' => $project->outcome->isEmpty(),
            ])->filter()->keys();

            $gaps += $missing->count();

            $rows[] = [
                $project->name,
                $missing->isEmpty()
                    ? '<fg=green>complete</>'
                    : '<fg=yellow>'.$missing->implode(', ').'</>',
            ];
        }

        $this->table(['Project', 'Missing'], $rows);

        return $gaps;
    }

    protected function auditClients(): int
    {
        $withoutLogo = Client::whereNull('logo_path')->count();

        $this->components->twoColumnDetail(
            'Clients without a logo file',
            $withoutLogo === 0 ? '<fg=green>0</>' : "<fg=yellow>{$withoutLogo}</>",
        );

        return $withoutLogo > 0 ? 1 : 0;
    }

    protected function auditServices(): int
    {
        $this->newLine();
        $this->components->twoColumnDetail('<fg=cyan;options=bold>SERVICES</>', '');

        $rows = [];
        $gaps = 0;

        foreach (Service::published()->ordered()->withCount('projects')->get() as $service) {
            // A service advertised with no work behind it is the gap a
            // serious buyer notices first.
            $unproven = $service->projects_count === 0;
            $thin = $service->projects_count === 1;

            if ($unproven || $service->body->isEmpty()) {
                $gaps++;
            }

            $rows[] = [
                (string) $service->title,
                match (true) {
                    $unproven => '<fg=red>0 — unproven</>',
                    $thin => '<fg=yellow>1 — thin</>',
                    default => "<fg=green>{$service->projects_count}</>",
                },
                $service->body->isEmpty() ? '<fg=yellow>missing</>' : '<fg=green>written</>',
            ];
        }

        $this->table(['Service', 'Case studies', 'Page copy'], $rows);

        return $gaps;
    }

    protected function auditSocialProof(): int
    {
        $gaps = 0;

        $testimonials = Testimonial::count();
        $posts = Post::published()->count();

        $this->components->twoColumnDetail(
            'Testimonials',
            $testimonials === 0 ? '<fg=red>none</>' : "<fg=green>{$testimonials}</>",
        );

        $this->components->twoColumnDetail(
            'Published posts',
            $posts === 0 ? '<fg=yellow>none</>' : "<fg=green>{$posts}</>",
        );

        $gaps += $testimonials === 0 ? 1 : 0;
        $gaps += $posts === 0 ? 1 : 0;

        return $gaps;
    }
}
