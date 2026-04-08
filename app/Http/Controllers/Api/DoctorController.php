<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Doctor\StoreDoctorRequest;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DoctorController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $allowedSorts = ['created_at', 'name_en', 'name_ar', 'specialization_en', 'specialization_ar'];
        $sortBy = in_array($request->input('sort_by'), $allowedSorts, true) ? $request->input('sort_by') : 'created_at';
        $sortDir = $request->input('sort_dir') === 'asc' ? 'asc' : 'desc';

        $doctors = Doctor::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($inner) use ($search) {
                    $inner->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%")
                        ->orWhere('specialization_en', 'like', "%{$search}%")
                        ->orWhere('specialization_ar', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortBy, $sortDir)
            ->paginate((int) $request->input('per_page', 10));

        return $this->successResponse(
            $this->paginatedData($doctors, DoctorResource::collection($doctors->items())),
        );
    }

    public function store(StoreDoctorRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('doctors', 'public');
        }

        $doctor = Doctor::create($data);

        return $this->successResponse(new DoctorResource($doctor), 'Doctor created successfully.', 201);
    }

    public function show(Doctor $doctor): JsonResponse
    {
        return $this->successResponse(new DoctorResource($doctor));
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($doctor->image) {
                Storage::disk('public')->delete($doctor->image);
            }

            $data['image'] = $request->file('image')->store('doctors', 'public');
        }

        $doctor->update($data);

        return $this->successResponse(new DoctorResource($doctor->fresh()), 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor): JsonResponse
    {
        if ($doctor->image) {
            Storage::disk('public')->delete($doctor->image);
        }

        $doctor->delete();

        return $this->successResponse(null, 'Doctor deleted successfully.');
    }
}
