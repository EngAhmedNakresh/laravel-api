<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\HomeContent\UpdateHomeContentRequest;
use App\Http\Resources\HomeContentResource;
use App\Models\HomeContent;
use Illuminate\Http\JsonResponse;

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

        $content->update($request->validated());

        return $this->successResponse(new HomeContentResource($content->fresh()), 'Home content updated successfully.');
    }
}
