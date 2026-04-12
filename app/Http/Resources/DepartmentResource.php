<?php

namespace App\Http\Resources;

use App\Support\PublicAssetUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lang = app()->getLocale();
        $cardImage = $this->publicImageUrl($this->card_image);
        $detailImage = $this->publicImageUrl($this->detail_image ?? $this->card_image);
        $secondaryDetailImage = $this->publicImageUrl($this->detail_image_secondary ?? $this->detail_image ?? $this->card_image);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->getLocalized('name', $lang),
            'short_description' => $this->getLocalized('short_description', $lang),
            'description' => $this->getLocalized('description', $lang),
            'card_image' => $cardImage,
            'detail_image' => $detailImage,
            'detail_image_secondary' => $secondaryDetailImage,
            'icon' => $this->icon,
            'feature_one' => $this->getLocalized('feature_one', $lang),
            'feature_two' => $this->getLocalized('feature_two', $lang),
            'hero_badge' => $this->getLocalized('hero_badge', $lang),
            'hero_title' => $this->getLocalized('hero_title', $lang),
            'hero_text' => $this->getLocalized('hero_text', $lang),
            'stats' => collect($this->stats ?? [])->map(fn ($item) => [
                'value' => $item['value'] ?? null,
                'text' => $item["text_{$lang}"] ?? $item['text_en'] ?? $item['text_ar'] ?? null,
            ])->values(),
            'primary_cta' => $this->getLocalized('primary_cta', $lang),
            'secondary_cta' => $this->getLocalized('secondary_cta', $lang),
            'floating_title' => $this->getLocalized('floating_title', $lang),
            'floating_text' => $this->getLocalized('floating_text', $lang),
            'services_title' => $this->getLocalized('services_title', $lang),
            'services_text' => $this->getLocalized('services_text', $lang),
            'services_list' => collect($this->services_list ?? [])->map(fn ($item) => [
                'icon' => $item['icon'] ?? 'bi bi-check2-circle',
                'title' => $item['title'][$lang] ?? $item['title']['en'] ?? $item['title']['ar'] ?? null,
                'text' => $item['text'][$lang] ?? $item['text']['en'] ?? $item['text']['ar'] ?? null,
            ])->values(),
            'expertise_title' => $this->getLocalized('expertise_title', $lang),
            'expertise_lead' => $this->getLocalized('expertise_lead', $lang),
            'expertise_list' => collect($this->expertise_list ?? [])->map(fn ($item) => $item[$lang] ?? $item['en'] ?? $item['ar'] ?? null)->values(),
            'emergency_label' => $this->getLocalized('emergency_label', $lang),
            'appointments_label' => $this->getLocalized('appointments_label', $lang),
            'appointments_value' => $this->getLocalized('appointments_value', $lang),
            'contact_phone' => $this->contact_phone,
            'created_at' => $this->created_at,
        ];
    }

    private function publicImageUrl(?string $path): ?string
    {
        return PublicAssetUrl::from($path);
    }
}
