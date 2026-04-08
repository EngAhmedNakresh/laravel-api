<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Feedback\StoreFeedbackRequest;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $feedback = Feedback::query()
            ->with('user.patient')
            ->latest()
            ->paginate((int) $request->input('per_page', 10));

        return $this->successResponse(
            $this->paginatedData($feedback, FeedbackResource::collection($feedback->items())),
        );
    }

    public function store(StoreFeedbackRequest $request): JsonResponse
    {
        $feedback = Feedback::create([
            'user_id' => $request->user()->id,
            'message' => $request->input('message'),
            'rating' => $request->integer('rating'),
        ]);

        return $this->successResponse(new FeedbackResource($feedback->load('user.patient')), 'Feedback submitted successfully.', 201);
    }
}
