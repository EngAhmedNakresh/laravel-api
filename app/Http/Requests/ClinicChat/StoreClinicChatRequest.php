<?php

namespace App\Http\Requests\ClinicChat;

use Illuminate\Foundation\Http\FormRequest;

class StoreClinicChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:4000'],
            'history' => ['nullable', 'array', 'max:20'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,assistant,system'],
            'history.*.message' => ['required_with:history', 'string', 'max:4000'],
        ];
    }
}
