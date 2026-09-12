<?php

namespace App\Http\Controllers;

use App\Actions\CaptureLead;
use App\Http\Requests\StoreLeadRequest;
use App\Support\Nav;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show(Request $request): View
    {
        return view('pages.contact', [
            'services' => Nav::services(),
            // Pre-selects the service when arriving from a service page.
            'selectedService' => $request->query('service'),
        ]);
    }

    public function store(StoreLeadRequest $request, CaptureLead $capture): RedirectResponse
    {
        $capture->handle($request->validated(), $request);

        return to_route('contact.thanks');
    }

    public function thanks(): View
    {
        return view('pages.contact-thanks');
    }
}
