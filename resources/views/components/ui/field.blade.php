@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'options' => null,
    'rows' => null,
    'placeholder' => null,
])

@php
    // See the note in newsletter-form: $errors is absent on error pages.
    $errors ??= new \Illuminate\Support\ViewErrorBag;

    $id = 'field-'.$name;
    $hasError = $errors->has($name);
    $describedBy = $hasError ? "{$id}-error" : null;

    $base = 'w-full rounded-xl bg-surface px-4 py-3 text-content ring-1 transition-colors placeholder:text-content-subtle focus:outline-none focus-visible:ring-2';
    $ring = $hasError ? 'ring-red-500/60 focus-visible:ring-red-500' : 'ring-hairline focus-visible:ring-accent-400';
@endphp

<div {{ $attributes->class(['flex flex-col gap-2']) }}>
    <label for="{{ $id }}" class="text-sm font-medium text-content">
        {{ $label }}
        @unless ($required)
            <span class="ms-1 text-xs font-normal text-content-subtle">{{ __('contact.placeholders.optional') }}</span>
        @endunless
    </label>

    @if ($options !== null)
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            @if ($required) required @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"
            class="{{ $base }} {{ $ring }}"
        >
            <option value="">{{ __('contact.placeholders.choose') }}</option>
            @foreach ($options as $value => $optionLabel)
                <option value="{{ $value }}" @selected(old($name, $placeholder) === $value)>{{ $optionLabel }}</option>
            @endforeach
        </select>
    @elseif ($rows)
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            @if ($required) required @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"
            placeholder="{{ $placeholder }}"
            class="{{ $base }} {{ $ring }} resize-y"
        >{{ old($name) }}</textarea>
    @else
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name) }}"
            @if ($required) required @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"
            placeholder="{{ $placeholder }}"
            autocomplete="{{ match ($name) {
                'name' => 'name',
                'email' => 'email',
                'phone' => 'tel',
                'company' => 'organization',
                default => 'off',
            } }}"
            class="{{ $base }} {{ $ring }}"
        >
    @endif

    @error($name)
        <p id="{{ $id }}-error" class="text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>
