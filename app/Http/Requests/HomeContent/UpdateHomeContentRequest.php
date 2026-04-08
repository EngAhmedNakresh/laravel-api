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
        ];
    }
}