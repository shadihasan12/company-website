<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline response security headers.
 *
 * A strict Content-Security-Policy is deliberately absent. Alpine
 * evaluates expressions with `new Function`, so any CSP tight enough to be
 * worth having would need `unsafe-eval`, and a policy carrying both
 * `unsafe-eval` and `unsafe-inline` provides close to no protection while
 * implying it does. Adding one means moving to Alpine's CSP build first.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = [
            // Stops browsers guessing a type and executing an upload as script.
            'X-Content-Type-Options' => 'nosniff',
            // Clickjacking: nothing here is meant to be framed.
            'X-Frame-Options' => 'SAMEORIGIN',
            // Send the origin cross-site, the full path same-site.
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            // Nothing on this site needs these.
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=(), usb=()',
            'X-Permitted-Cross-Domain-Policies' => 'none',
        ];

        if ($request->secure()) {
            // Only over HTTPS: sending HSTS over plain HTTP is ignored, and
            // setting it in local development would pin localhost.
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($headers as $header => $value) {
            $response->headers->set($header, $value, replace: false);
        }

        return $response;
    }
}
