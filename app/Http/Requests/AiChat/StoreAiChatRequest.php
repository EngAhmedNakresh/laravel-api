<?php

namespace App\Http\Requests\AiChat;

use Illuminate\Foundation\Http\FormRequest;

class StoreAiChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:4000'],
            'conversation_id' => ['nullable', 'integer', 'exists:conversations,id'],
        ];
    }
}
