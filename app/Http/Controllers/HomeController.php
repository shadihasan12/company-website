<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.home', [
            'services' => Service::published()->ordered()->get(),
            'featuredProjects' => Project::published()
                ->featured()
                ->ordered()
                ->with(['client', 'industry', 'technologies'])
                ->get(),
        ]);
    }
}
