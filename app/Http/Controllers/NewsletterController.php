<?php

namespace App\Http\Controllers;

use App\Actions\CaptureLead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request, CaptureLead $capture): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:190'],
            'website' => ['prohibited'],
        ], [
            'website.prohibited' => __('contact.errors.spam'),
        ]);

        $capture->handle([
            // The newsletter form asks for an address and nothing else;
            // anything more is friction on a low-intent action.
            'name' => strtok($validated['email'], '@'),
            'email' => $validated['email'],
        ], $request, source: 'newsletter');

        return back()->with('newsletter', __('contact.newsletter.done'));
    }
}
