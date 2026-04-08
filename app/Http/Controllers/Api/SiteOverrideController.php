<?php

namespace App\Http\Controllers\Api;

use App\Models\SiteOverride;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            'overrides' => ['required', 'array'],
        ]);

        $record = SiteOverride::ensureSeeded();
        $record->update([
            'overrides' => array_replace(SiteOverride::defaults(), $record->overrides ?? [], $data['overrides']),
        ]);

        return $this->successResponse([
            'overrides' => $record->fresh()->overrides,
        ], 'Site settings updated successfully.');
    }
}
