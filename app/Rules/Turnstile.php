<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Validates a Cloudflare Turnstile token.
 *
 * Skipped entirely when no secret is configured, so the site captures
 * leads out of the box and hardens the moment keys are supplied. The
 * honeypot, minimum fill time and rate limiter run regardless.
 */
class Turnstile implements ValidationRule
{
    public static function isConfigured(): bool
    {
        return filled(config('services.turnstile.secret'))
            && filled(config('services.turnstile.site_key'));
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! static::isConfigured()) {
            return;
        }

        if (blank($value)) {
            $fail(__('contact.errors.captcha'));

            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => config('services.turnstile.secret'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);
        } catch (\Throwable $exception) {
            // Cloudflare being unreachable must not cost a real enquiry, so
            // the submission is allowed through and the failure recorded.
            Log::warning('Turnstile verification unreachable', ['error' => $exception->getMessage()]);

            return;
        }

        if (! $response->json('success', false)) {
            $fail(__('contact.errors.captcha'));
        }
    }
}
