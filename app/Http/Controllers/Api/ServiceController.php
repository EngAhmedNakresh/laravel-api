<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $allowedSorts = ['created_at', 'name_en', 'name_ar'];
        $sortBy = in_array($request->input('sort_by'), $allowedSorts, true) ? $request->input('sort_by') : 'created_at';
        $sortDir = $request->input('sort_dir') === 'asc' ? 'asc' : 'desc';

        $services = Service::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('name_en', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('description_en', 'like', "%{$search}%")
                    ->orWhere('description_ar', 'like', "%{$search}%");
            })
            ->orderBy($sortBy, $sortDir)
            ->paginate((int) $request->input('per_page', 10));

        return $this->successResponse(
            $this->paginatedData($services, ServiceResource::collection($services->items())),
        );
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = Service::create($request->validated());

        return $this->successResponse(new ServiceResource($service), 'Service created successfully.', 201);
    }

    public function show(Service $service): JsonResponse
    {
        return $this->successResponse(new ServiceResource($service));
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $service->update($request->validated());

        return $this->successResponse(new ServiceResource($service->fresh()), 'Service updated successfully.');
    }

    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        return $this->successResponse(null, 'Service deleted successfully.');
    }
}
