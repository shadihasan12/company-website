<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use App\Models\Project;
use App\Models\Service;
use App\Models\Technology;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['service', 'industry', 'technology']);

        $projects = Project::published()
            ->ordered()
            ->with(['client', 'industry', 'technologies'])
            // Filters run server-side on query parameters rather than in
            // JavaScript, so a filtered view is a real, shareable,
            // indexable URL.
            ->when($filters['service'] ?? null, fn ($query, $slug) => $query->whereHas(
                'services', fn ($inner) => $inner->where('slug', $slug),
            ))
            ->when($filters['industry'] ?? null, fn ($query, $slug) => $query->whereHas(
                'industry', fn ($inner) => $inner->where('slug', $slug),
            ))
            ->when($filters['technology'] ?? null, fn ($query, $slug) => $query->whereHas(
                'technologies', fn ($inner) => $inner->where('slug', $slug),
            ))
            ->paginate(12)
            ->withQueryString();

        return view('pages.work.index', [
            'projects' => $projects,
            'filters' => array_filter($filters),
            // Only facets that would actually return something, so the bar
            // never offers a filter that leads to an empty page.
            'services' => Service::published()->ordered()
                ->whereHas('projects', fn ($query) => $query->published())->get(),
            'industries' => Industry::ordered()
                ->whereHas('projects', fn ($query) => $query->published())->get(),
            'technologies' => Technology::ordered()
                ->whereHas('projects', fn ($query) => $query->published())->get(),
        ]);
    }

    public function show(Project $project): View
    {
        abort_unless($project->is_published, 404);

        $project->load(['client', 'industry', 'services', 'technologies', 'testimonials']);

        return view('pages.work.show', [
            'project' => $project,
            'testimonial' => $project->testimonials->first(),
            'next' => $this->nextProject($project),
        ]);
    }

    /**
     * The following case study, wrapping around at the end so the last
     * project is never a dead end.
     */
    protected function nextProject(Project $project): ?Project
    {
        $published = Project::published()->ordered()->get(['id', 'slug', 'name', 'hero_image_path']);

        if ($published->count() < 2) {
            return null;
        }

        $position = $published->search(fn (Project $candidate) => $candidate->is($project));

        return $published[($position + 1) % $published->count()];
    }
}
