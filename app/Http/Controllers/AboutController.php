<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Support\SiteStats;
use Illuminate\Contracts\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.about', [
            'stats' => SiteStats::all(),
            'clients' => Client::named()->ordered()->get(),
        ]);
    }
}
