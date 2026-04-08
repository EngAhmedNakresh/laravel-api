<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ClinicChat\StoreClinicChatRequest;
use App\Services\OllamaClinicChatService;
use Illuminate\Http\JsonResponse;

class ClinicChatController extends ApiController
{
    public function __construct(
        private readonly OllamaClinicChatService $ollamaClinicChatService,
    ) {
    }

    public function store(StoreClinicChatRequest $request): JsonResponse
    {
        $reply = $this->ollamaClinicChatService->reply(
            (string) $request->input('message'),
            $request->input('history', []),
            app()->getLocale(),
        );

        return $this->successResponse([
            'reply' => $reply,
        ]);
    }
}
