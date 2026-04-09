<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $imageRules = $this->hasFile('image')
            ? ['nullable', 'file', 'image', 'max:2048']
            : ['nullable', 'string', 'max:255'];

        return [
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'image' => $imageRules,
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
