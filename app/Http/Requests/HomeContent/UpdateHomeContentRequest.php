<?php

namespace App\Http\Requests\HomeContent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hero' => ['required', 'array'],
            'about' => ['required', 'array'],
            'sections' => ['required', 'array'],
            'featured_department_images' => ['sometimes', 'array'],
            'featured_department_images.*' => ['nullable', 'file', 'image', 'max:2048'],
            'services_spotlight_circle_images' => ['sometimes', 'array'],
            'services_spotlight_circle_images.*' => ['nullable', 'file', 'image', 'max:2048'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $normalized = [];

        foreach (['hero', 'about', 'sections'] as $key) {
            $value = $this->input($key);

            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $normalized[$key] = $decoded;
            }
        }

        if ($normalized !== []) {
            $this->merge($normalized);
        }
    }
}
