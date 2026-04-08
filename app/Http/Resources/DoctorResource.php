<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lang = app()->getLocale();

        return [
            'id' => $this->id,
            'name' => $this->getLocalized('name', $lang),
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'specialization' => $this->getLocalized('specialization', $lang),
            'specialization_en' => $this->specialization_en,
            'specialization_ar' => $this->specialization_ar,
            'bio' => $this->getLocalized('bio', $lang),
            'bio_en' => $this->bio_en,
            'bio_ar' => $this->bio_ar,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at,
        ];
    }
}
