<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_en' => ['sometimes', 'required', 'string', 'max:255'],
            'name_ar' => ['sometimes', 'required', 'string', 'max:255'],
            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'feature_one_en' => ['nullable', 'string', 'max:255'],
            'feature_one_ar' => ['nullable', 'string', 'max:255'],
            'feature_two_en' => ['nullable', 'string', 'max:255'],
            'feature_two_ar' => ['nullable', 'string', 'max:255'],
            'cta_en' => ['nullable', 'string', 'max:255'],
            'cta_ar' => ['nullable', 'string', 'max:255'],
        ];
    }
}
