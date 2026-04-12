<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\HomeContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HomeContentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_featured_department_images_in_home_content(): void
    {
        config(['app.url' => 'http://localhost:8000']);
        Storage::fake('public');

        $content = HomeContent::ensureSeeded();
        $editor = [
            'hero' => $content->hero,
            'about' => $content->about,
            'sections' => $content->sections,
        ];

        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->put('/api/dashboard/home-content', [
            'hero' => json_encode($editor['hero'], JSON_UNESCAPED_UNICODE),
            'about' => json_encode($editor['about'], JSON_UNESCAPED_UNICODE),
            'sections' => json_encode($editor['sections'], JSON_UNESCAPED_UNICODE),
            'featured_department_images' => [
                UploadedFile::fake()->create('cardiology.jpg', 120, 'image/jpeg'),
                UploadedFile::fake()->create('neurology.jpg', 120, 'image/jpeg'),
            ],
        ]);

        $response->assertOk();

        $fresh = HomeContent::query()->firstOrFail();
        $firstImage = data_get($fresh->sections, 'departments.featured.0.image');
        $secondImage = data_get($fresh->sections, 'departments.featured.1.image');

        $this->assertIsString($firstImage);
        $this->assertIsString($secondImage);
        $this->assertStringStartsWith('home/featured-departments/', $firstImage);
        $this->assertStringStartsWith('home/featured-departments/', $secondImage);
        Storage::disk('public')->assertExists($firstImage);
        Storage::disk('public')->assertExists($secondImage);

        $response->assertJsonPath('data.sections.departments.featured.0.image', 'http://localhost:8000/storage/' . $firstImage)
            ->assertJsonPath('data.sections.departments.featured.1.image', 'http://localhost:8000/storage/' . $secondImage);
    }

    public function test_admin_can_upload_services_spotlight_circle_images_in_home_content(): void
    {
        config(['app.url' => 'http://localhost:8000']);
        Storage::fake('public');

        $content = HomeContent::ensureSeeded();
        $editor = [
            'hero' => $content->hero,
            'about' => $content->about,
            'sections' => $content->sections,
        ];

        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->put('/api/dashboard/home-content', [
            'hero' => json_encode($editor['hero'], JSON_UNESCAPED_UNICODE),
            'about' => json_encode($editor['about'], JSON_UNESCAPED_UNICODE),
            'sections' => json_encode($editor['sections'], JSON_UNESCAPED_UNICODE),
            'services_spotlight_circle_images' => [
                UploadedFile::fake()->create('maternal.jpg', 120, 'image/jpeg'),
                UploadedFile::fake()->create('vaccination.jpg', 120, 'image/jpeg'),
                UploadedFile::fake()->create('emergency.jpg', 120, 'image/jpeg'),
                UploadedFile::fake()->create('technology.jpg', 120, 'image/jpeg'),
            ],
        ]);

        $response->assertOk();

        $fresh = HomeContent::query()->firstOrFail();
        $images = collect(range(0, 3))
            ->map(fn (int $index) => data_get($fresh->sections, "services_spotlight.circles.{$index}.image"));

        foreach ($images as $image) {
            $this->assertIsString($image);
            $this->assertStringStartsWith('home/services-spotlight-circles/', $image);
            Storage::disk('public')->assertExists($image);
        }

        foreach ($images as $index => $image) {
            $response->assertJsonPath("data.sections.services_spotlight.circles.{$index}.image", 'http://localhost:8000/storage/' . $image);
        }
    }
}
