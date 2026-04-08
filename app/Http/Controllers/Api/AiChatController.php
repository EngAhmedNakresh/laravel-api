<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AiChat\StoreAiChatRequest;
use App\Http\Resources\ConversationResource;
use App\Services\ConversationService;
use App\Services\OpenAIChatService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class AiChatController extends ApiController
{
    public function __construct(
        private readonly OpenAIChatService $openAIChatService,
        private readonly ConversationService $conversationService,
    ) {
    }

    public function store(StoreAiChatRequest $request): JsonResponse
    {
        try {
            $conversation = $this->conversationService->findOrCreate(
                $request->input('conversation_id'),
                $request->user(),
            );
        } catch (AuthorizationException $exception) {
            return $this->errorResponse($exception->getMessage(), 403);
        }

        $this->conversationService->addMessage($conversation, 'user', $request->input('message'));

        $reply = $this->openAIChatService->reply($request->input('message'), app()->getLocale());

        $this->conversationService->addMessage($conversation, 'ai', $reply);

        return $this->successResponse([
            'reply' => $reply,
            'conversation' => new ConversationResource($conversation->fresh()->load(['messages'])),
        ]);
    }
}
