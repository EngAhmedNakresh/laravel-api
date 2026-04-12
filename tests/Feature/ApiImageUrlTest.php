<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\HomeContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiImageUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctors_endpoint_returns_safe_image_fields(): void
    {
        config(['app.url' => 'http://localhost:8000']);
        Storage::disk('public')->put('doctors/real.webp', 'image');

        Doctor::query()->create([
            'name_en' => 'Dr. Existing',
            'name_ar' => 'د. موجود',
            'specialization_en' => 'Cardiology',
            'specialization_ar' => 'قلب',
            'bio_en' => 'Existing doctor',
            'bio_ar' => 'طبيب موجود',
            'image' => 'doctors/real.webp',
        ]);

        Doctor::query()->create([
            'name_en' => 'Dr. Missing',
            'name_ar' => 'د. مفقود',
            'specialization_en' => 'Dermatology',
            'specialization_ar' => 'جلدية',
            'bio_en' => 'Missing image doctor',
            'bio_ar' => 'طبيب صورته مفقودة',
            'image' => '/assets/img/health/staff-404.webp',
        ]);

        $response = $this->getJson('/api/doctors');

        $response->assertOk()
            ->assertJsonFragment([
                'name_en' => 'Dr. Existing',
                'image' => 'http://localhost:8000/storage/doctors/real.webp',
                'image_url' => 'http://localhost:8000/storage/doctors/real.webp',
            ])
            ->assertJsonFragment([
                'name_en' => 'Dr. Missing',
                'image' => 'http://localhost:8000/assets/img/placeholders/doctor.svg',
                'image_url' => 'http://localhost:8000/assets/img/placeholders/doctor.svg',
            ]);

        Storage::disk('public')->delete('doctors/real.webp');
    }

    public function test_home_content_endpoint_normalizes_missing_images_to_fallbacks(): void
    {
        config(['app.url' => 'http://localhost:8000']);

        HomeContent::query()->create(HomeContent::defaults());

        $response = $this->getJson('/api/home-content');

        $response->assertOk()
            ->assertJsonPath('data.hero.image', 'http://localhost:8000/assets/img/placeholders/generic.svg')
            ->assertJsonPath('data.about.image', 'http://localhost:8000/assets/img/placeholders/generic.svg')
            ->assertJsonPath('data.sections.services_spotlight.image', 'http://localhost:8000/assets/img/placeholders/generic.svg')
            ->assertJsonPath('data.sections.call_to_action.image', 'http://localhost:8000/assets/img/placeholders/generic.svg')
            ->assertJsonPath('data.sections.departments.featured.0.image', 'http://localhost:8000/assets/img/placeholders/generic.svg')
            ->assertJsonPath('data.sections.services_spotlight.circles.0.image', 'http://localhost:8000/assets/img/placeholders/generic.svg');
    }
}
