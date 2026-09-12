<?php

namespace App\Http\Requests;

use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScopeRequest extends FormRequest
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
        $estimator = config('site.estimator');

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:5000'],

            'service' => ['nullable', Rule::exists('services', 'slug')],
            'stage' => ['nullable', Rule::in($estimator['stages'])],
            'budget_range' => ['nullable', Rule::in(config('site.leads.budget_ranges'))],
            'timeline' => ['nullable', Rule::in(config('site.leads.timelines'))],

            // Answers are validated against the configured lists so the
            // payload can never hold values the site does not offer.
            'platforms' => ['nullable', 'array'],
            'platforms.*' => [Rule::in($estimator['platforms'])],
            'features' => ['nullable', 'array'],
            'features.*' => [Rule::in($estimator['features'])],

            'website' => ['prohibited'],
            'loaded_at' => ['required', 'string'],
            'cf-turnstile-response' => [Turnstile::isConfigured() ? 'required' : 'nullable', new Turnstile],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return ['website.prohibited' => __('contact.errors.spam')];
    }

    protected function passedValidation(): void
    {
        try {
            $loadedAt = (int) decrypt($this->input('loaded_at'));
        } catch (\Throwable) {
            abort(422, __('contact.errors.expired'));
        }

        abort_if(
            now()->timestamp - $loadedAt < (int) config('site.leads.min_fill_seconds'),
            422,
            __('contact.errors.too_fast'),
        );
    }

    /**
     * The wizard answers, shaped for the lead's payload column.
     *
     * @return array<string, mixed>
     */
    public function scope(): array
    {
        return array_filter([
            'stage' => $this->input('stage'),
            'platforms' => $this->input('platforms', []),
            'features' => $this->input('features', []),
        ]);
    }
}
