<?php

namespace Tests\Unit;

use App\Support\PublicAssetUrl;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicAssetUrlTest extends TestCase
{
    public function test_it_returns_an_absolute_url_for_existing_public_assets(): void
    {
        config(['app.url' => 'http://localhost:8000']);

        $url = PublicAssetUrl::from('/assets/img/placeholders/generic.svg');

        $this->assertSame('http://localhost:8000/assets/img/placeholders/generic.svg', $url);
    }

    public function test_it_returns_an_absolute_url_for_existing_storage_files(): void
    {
        config(['app.url' => 'http://localhost:8000']);
        Storage::fake('public');
        Storage::disk('public')->put('doctors/existing.webp', 'content');

        $url = PublicAssetUrl::from('doctors/existing.webp', 'doctor');

        $this->assertSame('http://localhost:8000/storage/doctors/existing.webp', $url);
    }

    public function test_it_falls_back_when_the_requested_image_does_not_exist(): void
    {
        config(['app.url' => 'http://localhost:8000']);

        $url = PublicAssetUrl::from('/assets/img/health/missing.webp', 'doctor');

        $this->assertSame('http://localhost:8000/assets/img/placeholders/doctor.svg', $url);
    }
}
