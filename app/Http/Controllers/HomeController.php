<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Industry;
use App\Models\Project;
use App\Models\Technology;
use App\Models\Testimonial;
use App\Support\Nav;
use App\Support\SiteStats;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.home', [
            // Counted rather than hardcoded so the hero's claim can never
            // drift away from what the portfolio actually contains.
            'shippedCount' => SiteStats::publishedProjects(),
            // Nav::services() rather than a fresh query: the header and
            // footer already load exactly these rows.
            'services' => Nav::services(),
            // Named clients only. The strip falls back to a wordmark until
            // a logo file is uploaded.
            'clients' => Client::named()->ordered()->get(),
            'stats' => SiteStats::all(),
            // Grouped by category so the strip reads as a stack rather
            // than an undifferentiated tag cloud.
            'technologies' => Technology::ordered()->get()->groupBy('category'),
            // Empty until real quotes arrive — the section hides itself
            // rather than shipping invented social proof.
            // Only sectors with published work — the section claims
            // experience, so it must be backed by a case study.
            'industries' => Industry::ordered()
                // whereHas rather than having(): withCount compiles to a
                // correlated subquery, not an aggregate, so HAVING has
                // nothing to group on.
                ->whereHas('projects', fn ($query) => $query->published())
                ->withCount(['projects' => fn ($query) => $query->published()])
                ->get(),
            'testimonials' => Testimonial::featured()->ordered()->with('client')->take(3)->get(),
            'featuredProjects' => Project::published()
                ->featured()
                ->ordered()
                ->with(['client', 'industry', 'technologies'])
                ->get(),
        ]);
    }
}
