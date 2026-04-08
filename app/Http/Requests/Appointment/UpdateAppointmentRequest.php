<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => ['sometimes', 'required', 'integer', 'exists:doctors,id'],
            'date' => ['sometimes', 'required', 'date', 'after_or_equal:today'],
            'time' => ['sometimes', 'required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['sometimes', Rule::in(['pending', 'confirmed', 'cancelled'])],
        ];
    }
}
