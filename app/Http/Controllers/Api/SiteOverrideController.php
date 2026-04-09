<?php

namespace App\Http\Controllers\Api;

use App\Models\SiteOverride;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteOverrideController extends ApiController
{
    public function show(): JsonResponse
    {
        $record = SiteOverride::ensureSeeded();

        return $this->successResponse([
            'overrides' => $record->overrides,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'overrides' => ['nullable', 'array'],
        ]);

        $record = SiteOverride::ensureSeeded();
        $overrides = is_array($data['overrides'] ?? null) ? $data['overrides'] : [];

        foreach ($this->imageDirectories() as $key => $directory) {
            $file = $request->file("overrides.$key") ?? $request->file($key);

            if (! $file) {
                continue;
            }

            $currentPath = $record->overrides[$key] ?? null;

            if ($this->shouldDeleteStoredFile($currentPath)) {
                Storage::disk('public')->delete($currentPath);
            }

            $overrides[$key] = $file->store($directory, 'public');
        }

        $record->update([
            'overrides' => array_replace(SiteOverride::defaults(), $record->overrides ?? [], $overrides),
        ]);

        return $this->successResponse([
            'overrides' => $record->fresh()->overrides,
        ], 'Site settings updated successfully.');
    }

    private function imageDirectories(): array
    {
        return [
            'heroImageUrl' => 'site-overrides/hero',
            'aboutImageUrl' => 'site-overrides/about',
            'servicesSpotlightImageUrl' => 'site-overrides/services-spotlight',
            'callToActionImageUrl' => 'site-overrides/call-to-action',
        ];
    }

    private function shouldDeleteStoredFile(?string $path): bool
    {
        if (! $path || str_starts_with($path, '/') || filter_var($path, FILTER_VALIDATE_URL)) {
            return false;
        }

        if (! str_starts_with($path, 'site-overrides/')) {
            return false;
        }

        return Storage::disk('public')->exists($path);
    }
}
