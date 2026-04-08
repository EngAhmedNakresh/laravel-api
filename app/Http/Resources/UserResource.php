<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $patient = $this->relationLoaded('patient') ? $this->patient : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role?->value ?? $this->role,
            'phone' => $patient?->phone ?? '',
            'address' => $patient?->address,
            'email_verified_at' => $this->email_verified_at,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'created_at' => $this->created_at,
        ];
    }
}