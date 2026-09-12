<?php

namespace App\Http\Requests;

use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:120'],
            'service' => ['nullable', Rule::exists('services', 'slug')],
            'budget_range' => ['nullable', Rule::in(config('site.leads.budget_ranges'))],
            'timeline' => ['nullable', Rule::in(config('site.leads.timelines'))],
            'message' => ['required', 'string', 'min:20', 'max:5000'],

            // Honeypot: the field is hidden, so a real browser leaves it
            // empty. Anything in it is a bot.
            'website' => ['prohibited'],

            // Encrypted timestamp of when the form was rendered.
            'loaded_at' => ['required', 'string'],

            'cf-turnstile-response' => [Turnstile::isConfigured() ? 'required' : 'nullable', new Turnstile],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'website.prohibited' => __('contact.errors.spam'),
            'message.min' => __('contact.errors.message_short'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'cf-turnstile-response' => __('contact.fields.captcha'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => trim((string) $this->input('email')),
        ]);
    }

    protected function passedValidation(): void
    {
        $minimum = (int) config('site.leads.min_fill_seconds');

        try {
            $loadedAt = (int) decrypt($this->input('loaded_at'));
        } catch (\Throwable) {
            // A tampered or stale token is treated as a failed submission
            // rather than a server error.
            abort(422, __('contact.errors.expired'));
        }

        // Bots post the instant the page parses. A person cannot read the
        // form, type a 20-character message and submit inside a few seconds.
        abort_if(
            now()->timestamp - $loadedAt < $minimum,
            422,
            __('contact.errors.too_fast'),
        );
    }
}
