<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\SiteOverride;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SiteOverrideControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_site_overrides_with_existing_image_urls(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/site-overrides', [
            'overrides' => [
                'heroImageUrl' => 'http://localhost:8000/assets/img/placeholders/generic.svg',
                'aboutImageUrl' => '/assets/img/placeholders/generic.svg',
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.overrides.heroImageUrl', 'http://localhost:8000/assets/img/placeholders/generic.svg')
            ->assertJsonPath('data.overrides.aboutImageUrl', 'http://localhost:8000/assets/img/placeholders/generic.svg');

        $this->assertSame(
            'http://localhost:8000/assets/img/placeholders/generic.svg',
            SiteOverride::query()->firstOrFail()->overrides['heroImageUrl']
        );
    }

    public function test_admin_can_upload_a_new_override_image(): void
    {
        config(['app.url' => 'http://localhost:8000']);
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->post('/api/admin/site-overrides', [
            'overrides' => [
                'heroImageUrl' => UploadedFile::fake()->create('hero.jpg', 120, 'image/jpeg'),
            ],
        ]);

        $response->assertOk();

        $storedPath = SiteOverride::query()->firstOrFail()->overrides['heroImageUrl'];

        $this->assertIsString($storedPath);
        $this->assertStringStartsWith('site-overrides/hero/', $storedPath);
        Storage::disk('public')->assertExists($storedPath);
    }

    public function test_site_overrides_show_replaces_null_text_values_with_safe_defaults(): void
    {
        SiteOverride::query()->create([
            'overrides' => [
                'findDoctorTitleEn' => null,
                'findDoctorSearchButtonEn' => null,
                'heroTitleEn' => null,
            ],
        ]);

        $response = $this->getJson('/api/site-overrides');

        $response->assertOk()
            ->assertJsonPath('data.overrides.findDoctorTitleEn', '')
            ->assertJsonPath('data.overrides.findDoctorSearchButtonEn', '')
            ->assertJsonPath('data.overrides.heroTitleEn', '');
    }
}
