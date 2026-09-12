<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
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
            'role' => ['nullable', 'string', 'max:120'],
            // A link is asked for rather than a file upload: accepting
            // arbitrary documents from the public internet is a security
            // surface this site does not need yet.
            'portfolio' => ['nullable', 'url', 'max:500'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],

            'website' => ['prohibited'],
            'loaded_at' => ['required', 'string'],
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
}
