<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * The same gaps `php artisan content:audit` reports, surfaced where the
 * content is actually edited.
 */
class ContentGapsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        $projects = Project::published()->get();
        $missingEvidence = $projects->reject->is_evidenced->count();

        $unprovenServices = Service::published()->withCount('projects')
            ->get()->where('projects_count', 0)->count();

        $testimonials = Testimonial::count();
        $newLeads = Lead::where('status', 'new')->count();

        return [
            Stat::make('Case studies missing proof', $missingEvidence)
                ->description($missingEvidence === 0
                    ? 'Every case study has screenshots, results and a link'
                    : 'Screenshots, results or a public link')
                ->color($missingEvidence === 0 ? 'success' : 'warning'),

            Stat::make('Services with no case study', $unprovenServices)
                ->description($unprovenServices === 0
                    ? 'Every service is backed by real work'
                    : 'Advertised with nothing behind them')
                ->color($unprovenServices === 0 ? 'success' : 'danger'),

            Stat::make('Testimonials', $testimonials)
                ->description($testimonials === 0 ? 'None yet — the strongest missing signal' : 'Published')
                ->color($testimonials === 0 ? 'danger' : 'success'),

            Stat::make('New leads', $newLeads)
                ->description('Awaiting first contact')
                ->color($newLeads > 0 ? 'warning' : 'gray'),
        ];
    }
}
