<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lang = app()->getLocale();

        return [
            'id' => $this->id,
            'hero' => [
                'badges' => collect($this->hero['badges'] ?? [])->map(fn ($badge) => $this->translate($badge, $lang))->values(),
                'title' => $this->translate($this->hero['title'] ?? [], $lang),
                'subtitle' => $this->translate($this->hero['subtitle'] ?? [], $lang),
                'primary_cta' => $this->translate($this->hero['primary_cta'] ?? [], $lang),
                'secondary_cta' => $this->translate($this->hero['secondary_cta'] ?? [], $lang),
                'image' => data_get($this->hero, 'image'),
                'feature' => [
                    'title' => $this->translate(data_get($this->hero, 'feature.title', []), $lang),
                    'value' => $this->translate(data_get($this->hero, 'feature.value', []), $lang),
                    'caption' => $this->translate(data_get($this->hero, 'feature.caption', []), $lang),
                ],
                'rating' => [
                    'score' => data_get($this->hero, 'rating.score'),
                    'count' => $this->translate(data_get($this->hero, 'rating.count', []), $lang),
                ],
                'hotline_label' => $this->translate(data_get($this->hero, 'hotline_label', []), $lang),
                'hotline_number' => data_get($this->hero, 'hotline_number'),
            ],
            'about' => [
                'title' => $this->translate($this->about['title'] ?? [], $lang),
                'lead' => $this->translate($this->about['lead'] ?? [], $lang),
                'body' => $this->translate($this->about['body'] ?? [], $lang),
                'primary_cta' => $this->translate($this->about['primary_cta'] ?? [], $lang),
                'image' => data_get($this->about, 'image'),
                'card_title' => $this->translate(data_get($this->about, 'card_title', []), $lang),
                'card_text' => $this->translate(data_get($this->about, 'card_text', []), $lang),
                'badge_value' => data_get($this->about, 'badge_value'),
                'badge_label' => $this->translate(data_get($this->about, 'badge_label', []), $lang),
            ],
            'sections' => [
                'services' => [
                    'title' => $this->translate(data_get($this->sections, 'services.title', []), $lang),
                    'subtitle' => $this->translate(data_get($this->sections, 'services.subtitle', []), $lang),
                ],
                'services_page' => [
                    'title' => $this->translate(data_get($this->sections, 'services_page.title', []), $lang),
                    'subtitle' => $this->translate(data_get($this->sections, 'services_page.subtitle', []), $lang),
                ],
                'doctors' => [
                    'title' => $this->translate(data_get($this->sections, 'doctors.title', []), $lang),
                    'subtitle' => $this->translate(data_get($this->sections, 'doctors.subtitle', []), $lang),
                ],
                'feedback' => [
                    'title' => $this->translate(data_get($this->sections, 'feedback.title', []), $lang),
                    'subtitle' => $this->translate(data_get($this->sections, 'feedback.subtitle', []), $lang),
                    'cta' => $this->translate(data_get($this->sections, 'feedback.cta', []), $lang),
                ],
                'departments' => [
                    'title' => $this->translate(data_get($this->sections, 'departments.title', []), $lang),
                    'subtitle' => $this->translate(data_get($this->sections, 'departments.subtitle', []), $lang),
                    'featured' => collect(data_get($this->sections, 'departments.featured', []))->map(fn ($item) => [
                        'label' => $this->translate($item['label'] ?? [], $lang),
                        'title' => $this->translate($item['title'] ?? [], $lang),
                        'description' => $this->translate($item['description'] ?? [], $lang),
                        'feature_1' => $this->translate($item['feature_1'] ?? [], $lang),
                        'feature_2' => $this->translate($item['feature_2'] ?? [], $lang),
                        'cta' => $this->translate($item['cta'] ?? [], $lang),
                        'image' => $item['image'] ?? null,
                        'icon' => $item['icon'] ?? 'bi bi-heart-pulse',
                    ])->values(),
                    'highlights' => collect(data_get($this->sections, 'departments.highlights', []))->map(fn ($item) => [
                        'title' => $this->translate($item['title'] ?? [], $lang),
                        'description' => $this->translate($item['description'] ?? [], $lang),
                        'items' => collect($item['items'] ?? [])->map(fn ($subItem) => $this->translate($subItem, $lang))->values(),
                        'cta' => $this->translate($item['cta'] ?? [], $lang),
                        'icon' => $item['icon'] ?? 'bi bi-check-circle',
                    ])->values(),
                ],
                'services_spotlight' => [
                    'title' => $this->translate(data_get($this->sections, 'services_spotlight.title', []), $lang),
                    'subtitle' => $this->translate(data_get($this->sections, 'services_spotlight.subtitle', []), $lang),
                    'badge' => $this->translate(data_get($this->sections, 'services_spotlight.badge', []), $lang),
                    'image' => data_get($this->sections, 'services_spotlight.image'),
                    'cta' => $this->translate(data_get($this->sections, 'services_spotlight.cta', []), $lang),
                    'circles' => collect(data_get($this->sections, 'services_spotlight.circles', []))->map(fn ($item) => [
                        'title' => $this->translate($item['title'] ?? [], $lang),
                        'subtitle' => $this->translate($item['subtitle'] ?? [], $lang),
                        'image' => $item['image'] ?? null,
                    ])->values(),
                ],
                'emergency_banner' => [
                    'title' => $this->translate(data_get($this->sections, 'emergency_banner.title', []), $lang),
                    'subtitle' => $this->translate(data_get($this->sections, 'emergency_banner.subtitle', []), $lang),
                    'button' => $this->translate(data_get($this->sections, 'emergency_banner.button', []), $lang),
                    'phone' => data_get($this->sections, 'emergency_banner.phone'),
                ],                'call_to_action' => [
                    'title' => $this->translate(data_get($this->sections, 'call_to_action.title', []), $lang),
                    'subtitle' => $this->translate(data_get($this->sections, 'call_to_action.subtitle', []), $lang),
                    'primary_cta' => $this->translate(data_get($this->sections, 'call_to_action.primary_cta', []), $lang),
                    'secondary_cta' => $this->translate(data_get($this->sections, 'call_to_action.secondary_cta', []), $lang),
                    'image' => data_get($this->sections, 'call_to_action.image'),
                    'features' => collect(data_get($this->sections, 'call_to_action.features', []))->map(fn ($item) => [
                        'title' => $this->translate($item['title'] ?? [], $lang),
                        'text' => $this->translate($item['text'] ?? [], $lang),
                        'icon' => $item['icon'] ?? 'bi bi-shield-check',
                    ])->values(),
                    'contact_title' => $this->translate(data_get($this->sections, 'call_to_action.contact_title', []), $lang),
                    'contact_text' => $this->translate(data_get($this->sections, 'call_to_action.contact_text', []), $lang),
                    'phone' => data_get($this->sections, 'call_to_action.phone'),
                    'location_cta' => $this->translate(data_get($this->sections, 'call_to_action.location_cta', []), $lang),
                ],
                'find_doctor' => [
                    'title' => $this->translate(data_get($this->sections, 'find_doctor.title', []), $lang),
                    'subtitle' => $this->translate(data_get($this->sections, 'find_doctor.subtitle', []), $lang),
                    'search_title' => $this->translate(data_get($this->sections, 'find_doctor.search_title', []), $lang),
                    'search_subtitle' => $this->translate(data_get($this->sections, 'find_doctor.search_subtitle', []), $lang),
                    'name_placeholder' => $this->translate(data_get($this->sections, 'find_doctor.name_placeholder', []), $lang),
                    'all_specialties' => $this->translate(data_get($this->sections, 'find_doctor.all_specialties', []), $lang),
                    'search_button' => $this->translate(data_get($this->sections, 'find_doctor.search_button', []), $lang),
                    'view_details' => $this->translate(data_get($this->sections, 'find_doctor.view_details', []), $lang),
                    'book_now' => $this->translate(data_get($this->sections, 'find_doctor.book_now', []), $lang),
                    'schedule' => $this->translate(data_get($this->sections, 'find_doctor.schedule', []), $lang),
                    'view_all' => $this->translate(data_get($this->sections, 'find_doctor.view_all', []), $lang),
                    'circle_intro' => $this->translate(data_get($this->sections, 'find_doctor.circle_intro', []), $lang),
                    'card_meta' => collect(data_get($this->sections, 'find_doctor.card_meta', []))->map(fn ($item) => [
                        'status' => $item['status'] ?? 'available',
                        'rating' => $item['rating'] ?? '4.8',
                        'reviews' => $this->translate($item['reviews'] ?? [], $lang),
                        'experience' => $this->translate($item['experience'] ?? [], $lang),
                        'primary_mode' => $item['primary_mode'] ?? 'book',
                    ])->values(),
                ],
            ],
            'editor' => $request->user()?->isAdmin()
                ? [
                    'hero' => $this->hero,
                    'about' => $this->about,
                    'sections' => $this->sections,
                ]
                : null,
        ];
    }

    private function translate(array|string|null $value, string $lang): mixed
    {
        if (is_string($value) || $value === null) {
            return $value;
        }

        return $value[$lang] ?? $value['en'] ?? null;
    }
}
