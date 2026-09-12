<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class LegalController extends Controller
{
    public function __invoke(string $document): View
    {
        abort_unless(in_array($document, ['privacy', 'terms'], true), 404);

        return view('pages.legal', [
            'document' => $document,
            // Taken from the file's own modification time, so the date on
            // the page cannot drift away from when the wording last changed.
            'updatedAt' => Carbon::createFromTimestamp(
                filemtime(lang_path(app()->getLocale().'/legal.php')) ?: time(),
            ),
        ]);
    }
}
