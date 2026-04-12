<?php

namespace App\Http\Resources;

use App\Support\PublicAssetUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static function publicSummary(mixed $user): ?array
    {
        if (! $user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => PublicAssetUrl::from($user->avatar),
        ];
    }

    public function toArray(Request $request): array
    {
        $patient = $this->relationLoaded('patient') ? $this->patient : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => PublicAssetUrl::from($this->avatar),
            'role' => $this->role?->value ?? $this->role,
            'phone' => $patient?->phone ?? '',
            'address' => $patient?->address,
            'email_verified_at' => $this->email_verified_at,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'created_at' => $this->created_at,
        ];
    }
}
