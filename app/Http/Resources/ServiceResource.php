<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lang = app()->getLocale();

        return [
            'id' => $this->id,
            'name' => $this->getLocalized('name', $lang),
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'description' => $this->getLocalized('description', $lang),
            'description_en' => $this->description_en,
            'description_ar' => $this->description_ar,
            'image' => $this->publicImageUrl($this->image),
            'icon' => $this->icon,
            'feature_one' => $this->getLocalized('feature_one', $lang),
            'feature_one_en' => $this->feature_one_en,
            'feature_one_ar' => $this->feature_one_ar,
            'feature_two' => $this->getLocalized('feature_two', $lang),
            'feature_two_en' => $this->feature_two_en,
            'feature_two_ar' => $this->feature_two_ar,
            'cta' => $this->getLocalized('cta', $lang),
            'cta_en' => $this->cta_en,
            'cta_ar' => $this->cta_ar,
            'created_at' => $this->created_at,
        ];
    }

    private function publicImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL) || str_starts_with($path, '/')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}

