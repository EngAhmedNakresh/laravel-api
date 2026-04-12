<?php

namespace Tests\Feature;

use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DepartmentResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_department_show_falls_back_to_card_image_when_detail_images_are_missing(): void
    {
        config(['app.url' => 'http://localhost:8000']);

        $department = Department::query()->create([
            'slug' => 'test-department',
            'name_en' => 'Test Department',
            'name_ar' => 'قسم تجريبي',
            'card_image' => '/assets/img/placeholders/generic.svg',
            'detail_image' => null,
            'detail_image_secondary' => null,
        ]);

        $response = $this->getJson('/api/departments/'.$department->slug);

        $response->assertOk()
            ->assertJsonPath('data.card_image', 'http://localhost:8000/assets/img/placeholders/generic.svg')
            ->assertJsonPath('data.detail_image', 'http://localhost:8000/assets/img/placeholders/generic.svg')
            ->assertJsonPath('data.detail_image_secondary', 'http://localhost:8000/assets/img/placeholders/generic.svg');
    }

    public function test_admin_can_upload_department_detail_images_as_files(): void
    {
        config(['app.url' => 'http://localhost:8000']);
        Storage::fake('public');

        $department = Department::query()->create([
            'slug' => 'diagnostics',
            'name_en' => 'Diagnostics',
            'name_ar' => 'التشخيص',
        ]);

        $admin = \App\Models\User::factory()->create([
            'role' => \App\Enums\UserRole::Admin,
        ]);

        \Laravel\Sanctum\Sanctum::actingAs($admin);

        $response = $this->post('/api/departments/'.$department->id, [
            'slug' => 'diagnostics',
            'name_en' => 'Diagnostics',
            'name_ar' => 'التشخيص',
            'card_image' => UploadedFile::fake()->create('card.jpg', 120, 'image/jpeg'),
            'detail_image' => UploadedFile::fake()->create('detail.jpg', 120, 'image/jpeg'),
            'detail_image_secondary' => UploadedFile::fake()->create('detail-2.jpg', 120, 'image/jpeg'),
        ]);

        $response->assertOk();

        $department = $department->fresh();

        $this->assertStringStartsWith('departments/', $department->card_image);
        $this->assertStringStartsWith('departments/', $department->detail_image);
        $this->assertStringStartsWith('departments/', $department->detail_image_secondary);

        Storage::disk('public')->assertExists($department->card_image);
        Storage::disk('public')->assertExists($department->detail_image);
        Storage::disk('public')->assertExists($department->detail_image_secondary);
    }
}
