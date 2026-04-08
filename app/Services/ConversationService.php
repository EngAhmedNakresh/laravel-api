<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class ConversationService
{
    public function findOrCreate(?int $conversationId, ?User $user): Conversation
    {
        if (! $conversationId) {
            return Conversation::create([
                'user_id' => $user?->id,
            ]);
        }

        $conversation = Conversation::query()->findOrFail($conversationId);

        if ($user) {
            if (! $user->isAdmin() && $conversation->user_id !== $user->id) {
                throw new AuthorizationException('Unauthorized conversation access.');
            }
        } elseif ($conversation->user_id !== null) {
            throw new AuthorizationException('Unauthorized conversation access.');
        }

        return $conversation;
    }

    public function addMessage(Conversation $conversation, string $role, string $message): Message
    {
        return $conversation->messages()->create([
            'role' => $role,
            'message' => $message,
        ]);
    }
}
