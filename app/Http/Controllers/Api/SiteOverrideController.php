<?php

namespace App\Http\Controllers\Api;

use App\Models\SiteOverride;
use App\Support\PublicAssetUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
            'overrides.heroImageUrl' => $this->imageOverrideRules(),
            'overrides.aboutImageUrl' => $this->imageOverrideRules(),
            'overrides.servicesSpotlightImageUrl' => $this->imageOverrideRules(),
            'overrides.callToActionImageUrl' => $this->imageOverrideRules(),
            'heroImageUrl' => $this->imageOverrideRules(),
            'aboutImageUrl' => $this->imageOverrideRules(),
            'servicesSpotlightImageUrl' => $this->imageOverrideRules(),
            'callToActionImageUrl' => $this->imageOverrideRules(),
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
            'overrides' => SiteOverride::sanitizeOverrides(array_replace(
                SiteOverride::defaults(),
                $record->overrides ?? [],
                $overrides,
            )),
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
        if (! is_string($path)) {
            return $path;
        }

        return PublicAssetUrl::from($path);
    }

    private function imageOverrideRules(): array
    {
        return [
            'nullable',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value === null || $value === '') {
                    return;
                }

                if (is_string($value)) {
                    return;
                }

                if (! $value instanceof UploadedFile) {
                    $fail("The {$attribute} field must be a file or string.");

                    return;
                }

                $validator = Validator::make(
                    [$attribute => $value],
                    [$attribute => ['file', 'image', 'max:2048']]
                );

                if ($validator->fails()) {
                    foreach ($validator->errors()->get($attribute) as $message) {
                        $fail($message);
                    }
                }
            },
        ];
    }
}
