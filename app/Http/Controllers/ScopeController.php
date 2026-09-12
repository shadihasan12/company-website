<?php

namespace App\Http\Controllers;

use App\Actions\CaptureLead;
use App\Http\Requests\StoreScopeRequest;
use App\Support\Nav;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ScopeController extends Controller
{
    public function show(): View
    {
        return view('pages.scope', [
            'services' => Nav::services(),
        ]);
    }

    public function store(StoreScopeRequest $request, CaptureLead $capture): RedirectResponse
    {
        $capture->handle(
            [...$request->validated(), 'payload' => $request->scope()],
            $request,
            source: 'estimator',
        );

        return to_route('contact.thanks');
    }
}
