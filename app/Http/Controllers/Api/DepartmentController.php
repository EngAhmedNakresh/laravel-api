<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DepartmentController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $allowedSorts = ['created_at', 'name_en', 'name_ar', 'slug'];
        $sortBy = in_array($request->input('sort_by'), $allowedSorts, true) ? $request->input('sort_by') : 'created_at';
        $sortDir = $request->input('sort_dir') === 'asc' ? 'asc' : 'desc';

        $departments = Department::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($inner) use ($search) {
                    $inner->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%")
                        ->orWhere('short_description_en', 'like', "%{$search}%")
                        ->orWhere('short_description_ar', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortBy, $sortDir)
            ->paginate((int) $request->input('per_page', 12));

        return $this->successResponse(
            $this->paginatedData($departments, DepartmentResource::collection($departments->items())),
        );
    }

    public function show(string $slug): JsonResponse
    {
        $department = Department::query()->where('slug', $slug)->firstOrFail();

        return $this->successResponse(new DepartmentResource($department));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request, true);

        if ($request->hasFile('card_image')) {
            $data['card_image'] = $request->file('card_image')->store('departments', 'public');
        }

        $department = Department::create($data);

        return $this->successResponse(new DepartmentResource($department), 'Department created successfully.', 201);
    }

    public function update(Request $request, Department $department): JsonResponse
    {
        $data = $this->validated($request, false, $department->id);

        if ($request->hasFile('card_image')) {
            if ($this->shouldDeleteStoredFile($department->card_image)) {
                Storage::disk('public')->delete($department->card_image);
            }

            $data['card_image'] = $request->file('card_image')->store('departments', 'public');
        }

        $department->update($data);

        return $this->successResponse(new DepartmentResource($department->fresh()), 'Department updated successfully.');
    }

    public function destroy(Department $department): JsonResponse
    {
        if ($this->shouldDeleteStoredFile($department->card_image)) {
            Storage::disk('public')->delete($department->card_image);
        }

        $department->delete();

        return $this->successResponse(null, 'Department deleted successfully.');
    }

    private function validated(Request $request, bool $creating, ?int $departmentId = null): array
    {
        $this->normalizeJsonArrayInputs($request, [
            'stats',
            'services_list',
            'expertise_list',
        ]);

        $slugRule = ['required', 'string', 'max:255'];
        $slugRule[] = $creating ? 'unique:departments,slug' : "unique:departments,slug,{$departmentId}";
        $cardImageRules = $request->hasFile('card_image')
            ? ['nullable', 'file', 'image', 'max:2048']
            : ['nullable', 'string', 'max:255'];

        return $request->validate([
            'slug' => $creating ? $slugRule : ['sometimes', ...$slugRule],
            'name_en' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
            'name_ar' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
            'short_description_en' => ['nullable', 'string', 'max:255'],
            'short_description_ar' => ['nullable', 'string', 'max:255'],
            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'card_image' => $cardImageRules,
            'detail_image' => ['nullable', 'string', 'max:255'],
            'detail_image_secondary' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'feature_one_en' => ['nullable', 'string', 'max:255'],
            'feature_one_ar' => ['nullable', 'string', 'max:255'],
            'feature_two_en' => ['nullable', 'string', 'max:255'],
            'feature_two_ar' => ['nullable', 'string', 'max:255'],
            'hero_badge_en' => ['nullable', 'string', 'max:255'],
            'hero_badge_ar' => ['nullable', 'string', 'max:255'],
            'hero_title_en' => ['nullable', 'string', 'max:255'],
            'hero_title_ar' => ['nullable', 'string', 'max:255'],
            'hero_text_en' => ['nullable', 'string'],
            'hero_text_ar' => ['nullable', 'string'],
            'stats' => ['nullable', 'array'],
            'primary_cta_en' => ['nullable', 'string', 'max:255'],
            'primary_cta_ar' => ['nullable', 'string', 'max:255'],
            'secondary_cta_en' => ['nullable', 'string', 'max:255'],
            'secondary_cta_ar' => ['nullable', 'string', 'max:255'],
            'floating_title_en' => ['nullable', 'string', 'max:255'],
            'floating_title_ar' => ['nullable', 'string', 'max:255'],
            'floating_text_en' => ['nullable', 'string'],
            'floating_text_ar' => ['nullable', 'string'],
            'services_title_en' => ['nullable', 'string', 'max:255'],
            'services_title_ar' => ['nullable', 'string', 'max:255'],
            'services_text_en' => ['nullable', 'string'],
            'services_text_ar' => ['nullable', 'string'],
            'services_list' => ['nullable', 'array'],
            'expertise_title_en' => ['nullable', 'string', 'max:255'],
            'expertise_title_ar' => ['nullable', 'string', 'max:255'],
            'expertise_lead_en' => ['nullable', 'string'],
            'expertise_lead_ar' => ['nullable', 'string'],
            'expertise_list' => ['nullable', 'array'],
            'emergency_label_en' => ['nullable', 'string', 'max:255'],
            'emergency_label_ar' => ['nullable', 'string', 'max:255'],
            'appointments_label_en' => ['nullable', 'string', 'max:255'],
            'appointments_label_ar' => ['nullable', 'string', 'max:255'],
            'appointments_value_en' => ['nullable', 'string', 'max:255'],
            'appointments_value_ar' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function shouldDeleteStoredFile(?string $path): bool
    {
        if (! $path || str_starts_with($path, '/') || filter_var($path, FILTER_VALIDATE_URL)) {
            return false;
        }

        return Storage::disk('public')->exists($path);
    }

    private function normalizeJsonArrayInputs(Request $request, array $keys): void
    {
        $normalized = [];

        foreach ($keys as $key) {
            $value = $request->input($key);

            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $normalized[$key] = $decoded;
            }
        }

        if ($normalized !== []) {
            $request->merge($normalized);
        }
    }
}
