<?php

namespace App\Http\Controllers\Api;

use App\Models\SiteOverride;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class SiteOverrideController extends ApiController
{
    public function show(): JsonResponse
    {
        $record = SiteOverride::ensureSeeded();

        return $this->successResponse([
            'overrides' => $this->formatOverridesForResponse($record->overrides ?? []),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'overrides' => ['nullable', 'array'],
            'overrides.heroImageUrl' => ['nullable', 'file', 'image', 'max:2048'],
            'overrides.aboutImageUrl' => ['nullable', 'file', 'image', 'max:2048'],
            'overrides.servicesSpotlightImageUrl' => ['nullable', 'file', 'image', 'max:2048'],
            'overrides.callToActionImageUrl' => ['nullable', 'file', 'image', 'max:2048'],
            'heroImageUrl' => ['nullable', 'file', 'image', 'max:2048'],
            'aboutImageUrl' => ['nullable', 'file', 'image', 'max:2048'],
            'servicesSpotlightImageUrl' => ['nullable', 'file', 'image', 'max:2048'],
            'callToActionImageUrl' => ['nullable', 'file', 'image', 'max:2048'],
        ]);

        $record = SiteOverride::ensureSeeded();
        $overrides = is_array($data['overrides'] ?? null) ? $data['overrides'] : [];
        $nestedFiles = $request->file('overrides', []);

        foreach ($this->imageDirectories() as $key => $directory) {
            $file = $nestedFiles[$key] ?? $request->file("overrides.$key") ?? $request->file($key);

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
            'overrides' => $this->formatOverridesForResponse($record->fresh()->overrides ?? []),
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

    private function formatOverridesForResponse(array $overrides): array
    {
        foreach (array_keys($this->imageDirectories()) as $key) {
            $value = Arr::get($overrides, $key);
            Arr::set($overrides, $key, $this->publicImageUrl($value));
        }

        return $overrides;
    }

    private function publicImageUrl(mixed $path): mixed
    {
        if (! is_string($path) || $path === '') {
            return $path;
        }

        if (filter_var($path, FILTER_VALIDATE_URL) || str_starts_with($path, '/')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}



