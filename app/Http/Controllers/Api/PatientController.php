<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $patients = Patient::query()
            ->with('user.patient')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate((int) $request->input('per_page', 10));

        return $this->successResponse(
            $this->paginatedData($patients, PatientResource::collection($patients->items())),
        );
    }
}
