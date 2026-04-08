<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $conversations = Conversation::query()
            ->with(['messages', 'user'])
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest()
            ->paginate((int) $request->input('per_page', 10));

        return $this->successResponse(
            $this->paginatedData($conversations, ConversationResource::collection($conversations->items())),
        );
    }

    public function show(Conversation $conversation): JsonResponse
    {
        $this->authorize('view', $conversation);

        return $this->successResponse(new ConversationResource($conversation->load(['messages', 'user'])));
    }

    public function destroy(Conversation $conversation): JsonResponse
    {
        $this->authorize('delete', $conversation);

        $conversation->delete();

        return $this->successResponse(null, 'Conversation deleted successfully.');
    }
}
