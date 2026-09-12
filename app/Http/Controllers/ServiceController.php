<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Technology;
use Illuminate\Contracts\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('pages.services.index', [
            'services' => Service::published()->ordered()->withCount([
                'projects' => fn ($query) => $query->published(),
            ])->get(),
        ]);
    }

    public function show(Service $service): View
    {
        abort_unless($service->is_published, 404);

        $projects = $service->projects()
            ->published()
            ->ordered()
            ->with(['client', 'industry', 'technologies'])
            ->take(3)
            ->get();

        return view('pages.services.show', [
            'service' => $service,
            'projects' => $projects,
            // The stack shown is the one actually used on this service's
            // work. With no case studies yet the section simply hides,
            // rather than listing technologies we cannot point at.
            'technologies' => Technology::whereHas(
                'projects',
                fn ($query) => $query->published()->whereHas(
                    'services',
                    fn ($inner) => $inner->whereKey($service->getKey()),
                ),
            )->ordered()->get(),
            'others' => Service::published()
                ->ordered()
                ->whereKeyNot($service->getKey())
                ->get(),
        ]);
    }
}
