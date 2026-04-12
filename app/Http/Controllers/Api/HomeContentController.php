<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\HomeContent\UpdateHomeContentRequest;
use App\Http\Resources\HomeContentResource;
use App\Models\HomeContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class HomeContentController extends ApiController
{
    public function show(): JsonResponse
    {
        $content = HomeContent::ensureSeeded();

        return $this->successResponse(new HomeContentResource($content));
    }

    public function update(UpdateHomeContentRequest $request): JsonResponse
    {
        $content = HomeContent::ensureSeeded();
        $existingSections = $content->sections ?? [];
        $data = $request->validated();
        $sections = $data['sections'] ?? $existingSections;
        $featured = data_get($sections, 'departments.featured', data_get($existingSections, 'departments.featured', []));
        $spotlightCircles = data_get($sections, 'services_spotlight.circles', data_get($existingSections, 'services_spotlight.circles', []));

        foreach ($request->file('featured_department_images', []) as $index => $file) {
            if (! $file) {
                continue;
            }

            $currentPath = data_get($existingSections, "departments.featured.{$index}.image");

            if ($this->shouldDeleteStoredFile($currentPath)) {
                Storage::disk('public')->delete($currentPath);
            }

            data_set($featured, "{$index}.image", $file->store('home/featured-departments', 'public'));
        }

        foreach ($request->file('services_spotlight_circle_images', []) as $index => $file) {
            if (! $file) {
                continue;
            }

            $currentPath = data_get($existingSections, "services_spotlight.circles.{$index}.image");

            if ($this->shouldDeleteStoredFile($currentPath)) {
                Storage::disk('public')->delete($currentPath);
            }

            data_set($spotlightCircles, "{$index}.image", $file->store('home/services-spotlight-circles', 'public'));
        }

        data_set($sections, 'departments.featured', $featured);
        data_set($sections, 'services_spotlight.circles', $spotlightCircles);
        $data['sections'] = $sections;

        $content->update($data);

        return $this->successResponse(new HomeContentResource($content->fresh()), 'Home content updated successfully.');
    }

    private function shouldDeleteStoredFile(?string $path): bool
    {
        if (! $path || str_starts_with($path, '/') || filter_var($path, FILTER_VALIDATE_URL)) {
            return false;
        }

        return Storage::disk('public')->exists($path);
    }
}
