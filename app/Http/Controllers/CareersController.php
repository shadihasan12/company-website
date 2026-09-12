<?php

namespace App\Http\Controllers;

use App\Actions\CaptureLead;
use App\Http\Requests\StoreApplicationRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CareersController extends Controller
{
    public function show(): View
    {
        // The route is only registered when careers are enabled, but guard
        // here too so a cached route file cannot expose the page.
        abort_unless(config('site.careers.enabled'), 404);

        return view('pages.careers');
    }

    public function store(StoreApplicationRequest $request, CaptureLead $capture): RedirectResponse
    {
        abort_unless(config('site.careers.enabled'), 404);

        $capture->handle([
            ...$request->validated(),
            'payload' => array_filter([
                'role' => $request->input('role'),
                'portfolio' => $request->input('portfolio'),
            ]),
        ], $request, source: 'career');

        return back()->with('application', __('careers.done'));
    }
}
